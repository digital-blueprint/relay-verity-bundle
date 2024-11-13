<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Helpers;

class VerityResult
{
    public bool $validity = false;
    public string $message = '';
    public array $errors = [];
    public string $profileNameRequested = '';
    public string $profileNameUsed = '';

    public static function failed(string $flavour, array $errors): self
    {
        $vr = new self();
        $vr->profileNameRequested = $flavour;
        $vr->message = 'Error';
        $vr->errors = $errors;

        return $vr;
    }
}
