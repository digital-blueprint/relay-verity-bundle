<?php

declare(strict_types=1);

namespace Dbp\Relay\ValidationBundle\Event;

use Dbp\Relay\ValidationBundle\ApiResource\ValidationReport;
use Symfony\Contracts\EventDispatcher\Event;

class ValidateEvent extends Event
{
    public function __construct(protected ValidationReport $report)
    {
    }

    public function getReport(): ValidationReport
    {
        return $this->report;
    }
}
