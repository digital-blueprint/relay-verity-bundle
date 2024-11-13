<?php

declare(strict_types=1);

/**
 * PDF/A validation service.
 */

namespace Dbp\Relay\ValidationBundle\Service;

use Dbp\Relay\ValidationBundle\Helpers\ValidationResult;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PDFAValidationAPI implements ValidationProviderInterface, LoggerAwareInterface
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

    public function getVAlidationRequiredRole(string $profileName): string
    {
        // TODO: Implement getVAlidationRequiredRole() method.
        return 'validator-role';
    }

    public function validate($fileContent, $filename, $flavour, $mimetype): ValidationResult
    {
        // url
        $url = $this->serverUrl.'/api/validate/'.$flavour.'/';
        // Get the data size
        $fileSize = strlen($fileContent);
        if ($fileSize > $this->maxsize) {
            return ValidationResult::failed($flavour, ['size exceeded maxsize: '.$this->maxsize]);
        }

        // Calculate the sha1 checksum
        $sha1_checksum = sha1($fileContent);

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

        $result = new ValidationResult();
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
