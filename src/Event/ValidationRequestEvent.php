<?php

namespace Dbp\Relay\ValidationBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ValidationRequestEvent extends Event
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