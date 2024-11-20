<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Service;

use Dbp\Relay\VerityBundle\Helpers\VerityResult;

interface VerityProviderInterface
{
    /**
     * Perform the validation.
     *
     * @param string $fileContent the data/file content to validate
     * @param string $config      JSON encoded, depends on the service/API
     */
    public function validate(string $fileContent, string $filename, ?string $sha1sum, string $config, string $mimetype): VerityResult;

    /**
     * The role required for signing with the given profile.
     *
     * @throws \Exception
     */
    public function getVerityRequiredRole(string $profileName): string;
}
