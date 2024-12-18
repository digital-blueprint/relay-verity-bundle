<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Service;

use Dbp\Relay\VerityBundle\Helpers\VerityResult;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[AutoconfigureTag('dbp.relay.veritybundle.service')]
class DummyAPI implements VerityProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public static int $maxsize = 1024;

    public function __construct()
    {
    }

    /**
     * @throws \Exception
     */
    public function validate(string $fileContent, string $fileName, int $fileSize, ?string $sha1sum, string $config, string $mimetype): VerityResult
    {
        if (strlen($fileContent) > self::$maxsize) {
            $maxsize = self::$maxsize;
            throw new \Exception("File size exceeded maxsize: {$fileSize} > {$maxsize}");
        }

        $result = new VerityResult();
        $result->profileNameRequested = $config;
        $result->validity = true;
        $result->message = 'OK';

        return $result;
    }
}
