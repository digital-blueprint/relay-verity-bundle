<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Event;

use Dbp\Relay\VerityBundle\ApiResource\VerityReport;
use Symfony\Contracts\EventDispatcher\Event;

class VerityEvent extends Event
{
    public function __construct(protected VerityReport $report)
    {
    }

    public function getReport(): VerityReport
    {
        return $this->report;
    }
}
