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

    public function __construct()
    {
    }

    public function validate(string $fileContent, string $fileName, int $fileSize, ?string $sha1sum, string $config, string $mimetype): VerityResult
    {
        $result = new VerityResult();
        $result->profileNameRequested = $config;
        $result->validity = true;
        $result->message = 'OK';

        return $result;
    }
}
