<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Tests\Helper;

use Dbp\Relay\VerityBundle\Helpers\VerityResult;
use Dbp\Relay\VerityBundle\Service\VerityProviderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class DummyAPI implements VerityProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(private readonly string $serverUrl, private readonly int $maxsize)
    {
    }

    public function validate(string $fileContent, string $filename, string $flavour, string $mimetype): VerityResult
    {
        // Get the data size
        $fileSize = strlen($fileContent);
        if ($fileSize > $this->maxsize) {
            return VerityResult::failed($flavour, ['size exceeded maxsize: '.$this->maxsize]);
        }

        $result = new VerityResult();
        $result->profileNameRequested = $flavour;
        $result->validity = true;
        $result->message = 'OK';

        return $result;
    }

    public function getVerityRequiredRole(string $profileName): string
    {
        return 'validator-role';
    }
}
