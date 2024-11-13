<?php

declare(strict_types=1);

namespace Dbp\Relay\ValidationBundle\Tests;

use Dbp\Relay\ValidationBundle\Event\ValidateEvent;
use Dbp\Relay\ValidationBundle\Event\ValidationRequestEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Polyfill\Uuid\Uuid;

class ValidateEventTest extends KernelTestCase
{
    private $dispatcher;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->dispatcher = $container->get(EventDispatcherInterface::class);
        $this->dispatcher->addListener(ValidateEvent::class, static function (ValidateEvent $e) {
            echo '(+++ static function called +++)';
            ValidateEventTest::$event = $e;
        });
    }

    public function testEventSubscriber(): void
    {
        $data = base64_encode('data...');
        $uuid = Uuid::uuid_create();
        $fileName = 'test-003.txt';
        $event = new ValidationRequestEvent($uuid, $fileName, 'unit_test', $data);

        $result = $this->dispatcher->dispatch($event);

        //        dd(self::$event);
        $this->assertTrue($result->valid, 'MUST succeed.');
        $this->assertNotNull(self::$event, 'Event ValidateEvent not received.');
        $this->assertTrue(self::$event->getReport()->valid, 'MUST succeed.');
        $this->assertEquals($data, self::$event->getReport()->getData());
        $this->assertEquals($uuid, self::$event->getReport()->getUuid());
        $this->assertEquals($fileName, self::$event->getReport()->getFilename());
        $this->assertEquals('OK', self::$event->getReport()->getMessage());
    }

    public static ?ValidateEvent $event = null;
}
