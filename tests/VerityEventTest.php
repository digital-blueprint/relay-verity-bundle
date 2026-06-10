<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Tests;

use Dbp\Relay\VerityBundle\Event\VerityEvent;
use Dbp\Relay\VerityBundle\Event\VerityRequestEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;

class VerityEventTest extends KernelTestCase
{
    private $dispatcher;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->dispatcher = $container->get(EventDispatcherInterface::class);
        $this->dispatcher->addListener(VerityEvent::class, static function (VerityEvent $e) {
            VerityEventTest::$event = $e;
        });
    }

    public function testEventSubscriber(): void
    {
        $tempDir = sys_get_temp_dir();
        $filePath = tempnam($tempDir, 'dummy_');
        try {
            $data = 'data...';
            file_put_contents($filePath, $data);
            $uuid = Uuid::v4()->toRfc4122();
            $fileName = 'test-003.txt';
            $event = new VerityRequestEvent($uuid,
                $fileName,
                hash('sha256', $data),
                'unit_test',
                new File($filePath),
                'plain/text',
                strlen($data));

            $result = $this->dispatcher->dispatch($event);
            $this->assertTrue($result->valid, 'MUST succeed.');
            $this->assertNotNull(self::$event, 'Event ValidateEvent not received.');
            $this->assertTrue(self::$event->getReport()->valid, 'MUST succeed.');
            $this->assertEquals($uuid, self::$event->getReport()->getUuid());
            $this->assertEquals('OK', self::$event->getReport()->getMessage());
        } finally {
            unlink($filePath);
        }
    }

    public static ?VerityEvent $event = null;
}
