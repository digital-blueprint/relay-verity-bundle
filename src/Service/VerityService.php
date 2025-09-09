<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Service;

use Dbp\Relay\CoreBundle\Exception\ApiError;
use Dbp\Relay\VerityBundle\ApiResource\VerityReport;
use Dbp\Relay\VerityBundle\Event\VerityEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VerityService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ExpressionLanguage $expressionLanguage;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ConfigurationService $configurationService,
        private readonly HttpClientInterface $httpClient,
        private readonly VerityProviderInterfaceService $verityProviderInterfaceService)
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * @return string[]
     *
     * @psalm-return list{'validator-rule'}
     */
    public function getValidationRules(): array
    {
        return ['validator-rule'];
    }

    public function getVerityRequiredRole(string $profileName): string
    {
        // TODO: Implement getVerityRequiredRole() method.
        return 'validator-role';
    }

    public function validate($uuid, $file, $fileName, $fileSize, $fileHash, $profileName, $mimetype): VerityReport
    {
        $profile = $this->configurationService->getProfile($profileName);
        if ($profile === null) {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST,
                "Unknown profile \"$profileName\"!",
                'verity:create-report-missing-profile');
        }
        // Get the data size
        if ($fileSize === 0) {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST,
                'Parameter file size is 0 (zero).',
                'verity:create-report-file-size-zero');
        }
        if ($file === null || $file === '') {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST,
                'File content is empty.',
                'verity:create-report-file-content-empty');
        }
        if ($fileSize !== $file->getSize()) {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST,
                'Parameter file size mismatch.',
                'verity:create-report-file-size-mismatch');
        }
        if ($fileHash !== null && $fileHash !== hash_file('sha1', $file->getPathname())) {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST,
                'Parameter file hash mismatch.',
                'verity:create-report-file-hash-mismatch');
        }

        // Collect standard information about the document.
        $document = new \stdClass();
        $document->mimetype = $mimetype;
        $document->fileSize = $fileSize;
        $document->fileName = $fileName;
        $document->fileHash = $fileHash;

        $vars = ['document' => $document];
        $errors = [];
        foreach ($profile['checks'] as $name => $check) {
            $backend = $this->configurationService->getBackend($check['backend']);
            $className = $backend['validator'];
            $validator = $this->verityProviderInterfaceService->getService($className);
            $config = $check['config'];

            try {
                $vr = $validator->validate($file, $fileName, $fileSize, $fileHash, $config, $mimetype);
            } catch (\Exception $e) {
                throw ApiError::withDetails(Response::HTTP_BAD_REQUEST,
                    $e->getMessage(),
                    'verity:create-report-backend-exception');
            }
            $vars[$name] = $vr;
            if ($vr->errors) {
                $e = array_map(static function ($error) use ($name) { return "$name: $error"; }, $vr->errors);
                $errors = [...$errors, ...$e];
            }
        }

        $validity = $this->expressionLanguage->evaluate($profile['rule'], $vars);
        $report = new VerityReport($uuid);
        $report->setProfile($profileName);
        $report->setValid($validity);
        if ($errors) {
            $report->setErrors($errors);
            $report->setMessage('Has Errors');
        } else {
            $report->setMessage('OK');
        }

        $validateEvent = new VerityEvent($report);
        $this->eventDispatcher->dispatch($validateEvent);

        return $report;
    }
}
