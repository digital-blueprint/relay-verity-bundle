<?php

declare(strict_types=1);

namespace Dbp\Relay\ValidationBundle\Tests;

use Dbp\Relay\ValidationBundle\Event\ValidationRequestEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Polyfill\Uuid\Uuid;

class EventSubscriberTest extends KernelTestCase
{
    private $dispatcher;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->dispatcher = $container->get(EventDispatcherInterface::class);
    }

    public function testEventSubscriber(): void
    {
        $data = base64_encode('data...');
        $event = new ValidationRequestEvent(Uuid::uuid_create(), 'test-001.txt', 'unit_test', $data);

        $result = $this->dispatcher->dispatch($event);

        $this->assertTrue($result->valid, 'MUST succeed.');
    }

    public function testSizeExceeded(): void
    {
        $data = base64_encode('data.data.data.data.'); // more than 16 chars
        $event = new ValidationRequestEvent(Uuid::uuid_create(), 'test-002.txt', 'unit_test', $data);

        $result = $this->dispatcher->dispatch($event);

        $this->assertFalse($result->valid, 'MUST not succeed.');
        $this->assertNotEmpty($result->errors, 'Error message missing.');
    }

    public function testEmptyContent(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $data = base64_encode('');

        $event = new ValidationRequestEvent(Uuid::uuid_create(), 'test-003.txt', 'unit_test', $data);

        $this->dispatcher->dispatch($event);
    }

    public function testMissingProfile(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $data = base64_encode('data...');
        $event = new ValidationRequestEvent(Uuid::uuid_create(), 'test-004.txt', '', $data);

        $this->dispatcher->dispatch($event);
    }

    public function testUnknownProfile(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $data = base64_encode('data...');
        $event = new ValidationRequestEvent(Uuid::uuid_create(), 'test-005.txt', 'unknown???', $data);

        $this->dispatcher->dispatch($event);
    }
}
