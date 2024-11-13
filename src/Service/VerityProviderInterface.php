<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Service;

use Dbp\Relay\VerityBundle\Helpers\VerityResult;

interface VerityProviderInterface
{
    public function validate(string $fileContent, string $filename, string $flavour, string $mimetype): VerityResult;

    /**
     * The role required for signing with the given profile.
     *
     * @throws \Exception
     */
    public function getVerityRequiredRole(string $profileName): string;
}
