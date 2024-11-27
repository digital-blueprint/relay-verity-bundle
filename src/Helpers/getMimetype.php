<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Helpers;

/**
 * Get the mimetype of the content of a string.
 */
function getMimetype($content): ?string
{
    $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
    $result = $fileInfo->buffer($content);

    return substr($result, 0, strpos($result, ';') ?: 100);
}
