<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Service;

use Dbp\Relay\VerityBundle\Helpers\VerityResult;
use Symfony\Component\HttpFoundation\File\File;

interface VerityProviderInterface
{
    /**
     * Perform the validation.
     *
     * @param File   $file     the data/file content to validate
     * @param string $fileName the name associated with the file content
     * @param int    $fileSize the size of the file content
     * @param string $fileHash the mandatory sha256 hash of the file content
     * @param string $mimetype the mimetype of the file content
     */
    public function validate(File $file,
        string $fileName,
        int $fileSize,
        string $fileHash,
        string $config,
        string $mimetype): VerityResult;
}
