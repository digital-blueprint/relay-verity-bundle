<?php

declare(strict_types=1);

namespace Dbp\Relay\ValidationBundle\EventSubscriber;

use Dbp\Relay\ValidationBundle\ApiResource\ValidationReport;
use Dbp\Relay\ValidationBundle\Event\ValidationRequestEvent;
use Dbp\Relay\ValidationBundle\State\ValidationReportStateProcessor;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


#[AsEventListener]
class ValidationRequestEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ValidationReportStateProcessor $stateProcessor)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            ValidationRequestEvent::class => 'onValidationRequest',
        ];
    }

    public function onValidationRequest(ValidationRequestEvent $event): void
    {
        $validationReport = new ValidationReport($event->uuid);
        $validationReport->setFilename($event->filename);
        $validationReport->setProfile($event->profile);
        $validationReport->setData($event->data);

        $report = $this->stateProcessor->addItem($validationReport, []);

        $event->valid = $report->isValid();
        $event->message = $report->getMessage();
        $event->errors = $report->getErrors();
    }
}
