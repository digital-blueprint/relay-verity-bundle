<?php

declare(strict_types=1);

/**
 * PDF/A validation service.
 */

namespace Dbp\Relay\VerityBundle\Service;

use Dbp\Relay\VerityBundle\Helpers\VerityResult;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PDFAValidationAPI implements VerityProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly string $serverUrl,
        private readonly int $maxsize,
        private readonly HttpClientInterface $httpClient)
    {
    }

    public function getValidationRules(): array
    {
        return ['validator-rule'];
    }

    public function getVerityRequiredRole(string $profileName): string
    {
        // TODO: Implement getVerityRequiredRole() method.
        return 'validator-role';
    }

    public function validate($fileContent, $filename, $sha1sum, $config, $mimetype): VerityResult
    {
        $checkConfig = json_decode($config, true);
        if (!isset($checkConfig['flavour']) || $checkConfig['flavour'] === '') {
            throw new \Exception("Required config key 'flavour' is missing.");
        }
        $flavour = $checkConfig['flavour'];
        // url
        $url = $this->serverUrl.'/api/validate/'.$flavour.'/';
        // Get the data size
        $fileSize = strlen($fileContent);
        if ($fileSize > $this->maxsize) {
            return VerityResult::failed($flavour, ['size exceeded maxsize: '.$this->maxsize]);
        }

        // Calculate the sha1 checksum
        $sha1_checksum = sha1($fileContent);
        if ($sha1sum !== null && $sha1sum !== $sha1_checksum) {
            return VerityResult::failed($flavour, ['given sha1sum does not match']);
        }

        $fileHandle = fopen('data://text/plain,'.urlencode($fileContent), 'rb');
        stream_context_set_option($fileHandle, 'http', 'filename', $filename);
        stream_context_set_option($fileHandle, 'http', 'content_type', $mimetype);

        // Prepare the data for the API request
        $data = [
            'sha1Hex' => $sha1_checksum,
            'file' => $fileHandle,
        ];

        $response = null;
        try {
            $response = $this->httpClient->request('POST', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'X-File-Size: '.$fileSize,
                    'Content-Type: multipart/form-data',
                ],
                'body' => $data,
            ]);
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);
        } catch (TransportExceptionInterface $e) {
            if ($response instanceof \Symfony\Contracts\HttpClient\ResponseInterface) {
                $statusCode = $response->getStatusCode();
                $content = $response->getContent(false);
            } else {
                $statusCode = 500;
                $content = 'Internal Server Error';
            }
        }

        $result = new VerityResult();
        $result->profileNameRequested = $flavour;

        // Check if the request was successful
        if ($statusCode !== 200) {
            $result->validity = false;
            $result->message = 'Network Error';
            $result->errors[] = $statusCode.' '.$content;

            return $result;
        }

        $res = json_decode($content, true);
        $validationResult = $res['report']['jobs'][0]['validationResult'];

        $result->validity = $validationResult['compliant'];
        $result->message = $validationResult['statement'];
        $result->profileNameUsed = $validationResult['profileName'];
        if ($validationResult['details']['failedRules'] > 0) {
            foreach ($validationResult['details']['ruleSummaries'] as $summary) {
                if ($summary['ruleStatus'] === 'FAILED') {
                    $result->errors[] = $summary['description'];
                }
            }
        }

        return $result;
    }
}
