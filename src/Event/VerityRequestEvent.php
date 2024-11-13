<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class VerityRequestEvent extends Event
{
    public function __construct(
        public ?string $uuid,
        public ?string $filename,
        public ?string $profile,
        public ?string $data,
        public bool $valid = false,
        public string $message = '',
        public array $errors = [])
    {
    }
}
