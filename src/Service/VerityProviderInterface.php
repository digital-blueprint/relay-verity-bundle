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
     * @param string $fileName    the name associated with the file content
     * @param int    $fileSize    the size of the file content
     * @param string $sha1sum     the mandatory sha1 hash of the file content
     * @param string $mimetype    the mimetype of the file content
     */
    public function validate(string $fileContent,
        string $fileName,
        int $fileSize,
        string $sha1sum,
        string $config,
        string $mimetype): VerityResult;
}
