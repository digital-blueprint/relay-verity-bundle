<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\EventSubscriber;

use Dbp\Relay\VerityBundle\ApiResource\VerityReport;
use Dbp\Relay\VerityBundle\Event\VerityRequestEvent;
use Dbp\Relay\VerityBundle\Service\ValidationService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

#[AsEventListener]
class VerityRequestEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ValidationService $validationService)
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
        $report = $this->validationService->validate(
            $event->uuid,
            $event->fileContent,
            $event->fileName,
            $event->fileSize,
            $event->fileHash,
            $event->profileName,
            $event->mimetype);
        if ($report instanceof VerityReport) {
            $event->valid = $report->isValid();
            $event->message = $report->getMessage();
            $event->errors = $report->getErrors();
        }
    }
}
