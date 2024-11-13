<?php

declare(strict_types=1);

namespace Dbp\Relay\ValidationBundle\Service;

use Dbp\Relay\ValidationBundle\Helpers\ValidationResult;

interface ValidationProviderInterface
{
    public function validate(string $fileContent, string $filename, string $flavour, string $mimetype): ValidationResult;

    /**
     * The role required for signing with the given profile.
     *
     * @throws \Exception
     */
    public function getValidationRequiredRole(string $profileName): string;
}
