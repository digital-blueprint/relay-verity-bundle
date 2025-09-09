<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Event;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\EventDispatcher\Event;

class VerityRequestEvent extends Event
{
    public function __construct(
        public ?string $uuid,
        public ?string $fileName,
        public ?string $fileHash,
        public ?string $profileName,
        public ?File $file = null,
        public ?string $mimetype = null,
        public int $fileSize = 0,
        public bool $valid = false,
        public string $message = '',
        public array $errors = [])
    {
    }
}
