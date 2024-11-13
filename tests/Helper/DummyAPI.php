<?php

namespace Dbp\Relay\ValidationBundle\Tests\Helper;

use Dbp\Relay\ValidationBundle\Helpers\ValidationResult;
use Dbp\Relay\ValidationBundle\Service\ValidationProviderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class DummyAPI implements ValidationProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(private string $serverUrl, private int $maxsize)
    {
    }

    public function validate(string $fileContent, string $filename, string $flavour, string $mimetype): ValidationResult
    {
        // Get the data size
        $fileSize = strlen($fileContent);
        if ($fileSize > $this->maxsize) {
            return ValidationResult::failed($flavour, ['size exceeded maxsize: '.$this->maxsize]);
        }

        $result = new ValidationResult();
        $result->profileNameRequested = $flavour;
        $result->validity = true;
        $result->message = 'OK';

        return $result;
    }

    public function getValidationRequiredRole(string $profileName): string
    {
        return 'validator-role';
    }
}