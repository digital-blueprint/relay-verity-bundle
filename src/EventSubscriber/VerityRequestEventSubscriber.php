<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\EventSubscriber;

use Dbp\Relay\VerityBundle\ApiResource\VerityReport;
use Dbp\Relay\VerityBundle\Event\VerityRequestEvent;
use Dbp\Relay\VerityBundle\State\VerityReportStateProcessor;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

#[AsEventListener]
class VerityRequestEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly VerityReportStateProcessor $stateProcessor)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VerityRequestEvent::class => 'onVerityRequest',
        ];
    }

    public function onVerityRequest(VerityRequestEvent $event): void
    {
        $verityReport = new VerityReport($event->uuid);
        $verityReport->setFilename($event->filename);
        $verityReport->setProfile($event->profile);
        $verityReport->setData($event->data);

        $report = $this->stateProcessor->addItem($verityReport, []);

        $event->valid = $report->isValid();
        $event->message = $report->getMessage();
        $event->errors = $report->getErrors();
    }
}
