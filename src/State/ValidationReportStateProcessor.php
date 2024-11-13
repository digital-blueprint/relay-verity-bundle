<?php

declare(strict_types=1);

namespace Dbp\Relay\ValidationBundle\State;

use Dbp\Relay\CoreBundle\Rest\AbstractDataProcessor;
use Dbp\Relay\ValidationBundle\ApiResource\ValidationReport;
use Dbp\Relay\ValidationBundle\Event\ValidateEvent;
use Dbp\Relay\ValidationBundle\Service\ConfigurationService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ValidationReportStateProcessor extends AbstractDataProcessor
{
    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly HttpClientInterface $httpClient)
    {
        parent::__construct();
    }

    public function addItem($data, array $filters): mixed
    {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $profileName = $data->profile;
        if ($profileName === '') {
            throw new BadRequestHttpException('Missing "profile" attribute!');
        }

        $profile = $this->configurationService->getProfile($profileName);
        if ($profile === null) {
            throw new BadRequestHttpException("Unknown profile \"$profileName\"!");
        }

        //        $this->checkProfilePermissions($profileName);

        $uploadedFile = $data->data;

        // check if there is an uploaded file
        if (!$uploadedFile) {
            throw new BadRequestHttpException('Missing or empty "data" attribute!');
        }

        $content = base64_decode($uploadedFile, true);

        $mimetype = $data->getMimetype();
        $fileSize = strlen($content);

        // Collect standard information about the document.
        $document = new \stdClass();
        $document->mimetype = $mimetype;
        $document->size = $fileSize;
        $document->filename = $data->filename;

        $vars = ['document' => $document];
        $errors = [];

        foreach ($profile['checks'] as $name => $check) {
            $flavour = $check['flavour'];
            $backend = $this->configurationService->getBackend($check['backend']);
            $className = $backend['validator'];
            $validator = new $className($backend['url'], $backend['maxsize'], $this->httpClient);

            $vr = $validator->validate($content, $data->filename, $flavour, $mimetype);
            $vars[$name] = $vr;
            if ($vr->errors) {
                $e = array_map(static function ($error) use ($name, $flavour) { return "$name/$flavour: $error"; }, $vr->errors);
                $errors = [...$errors, ...$e];
            }
        }

        $expressionLanguage = new ExpressionLanguage();
        $validity = $expressionLanguage->evaluate($profile['rule'], $vars);

        $report = new ValidationReport($data->uuid);
        $report->setFilename($data->filename);
        $report->setData($data->data);
        $report->setProfile($data->profile);
        $report->setValid($validity);
        if ($errors) {
            $report->setErrors($errors);
            $report->setMessage('Has Errors');
        } else {
            $report->setMessage('OK');
        }

        $validateEvent = new ValidateEvent($report);
        $this->eventDispatcher->dispatch($validateEvent);

        return $report;
    }

//    protected function isUserGrantedOperationAccess(int $operation): bool
//    {
//        return true;
//    }
}
