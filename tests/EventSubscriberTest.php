<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Tests;

use Dbp\Relay\VerityBundle\Event\VerityRequestEvent;
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
        $event = new VerityRequestEvent(Uuid::uuid_create(), 'test-001.txt', null, 'unit_test', $data);

        $result = $this->dispatcher->dispatch($event);

        $this->assertTrue($result->valid, 'MUST succeed.');
    }

    public function testSizeExceeded(): void
    {
        $data = base64_encode('data.data.data.data.'); // more than 16 chars
        $event = new VerityRequestEvent(Uuid::uuid_create(), 'test-002.txt', null, 'unit_test', $data);

        $result = $this->dispatcher->dispatch($event);

        $this->assertFalse($result->valid, 'MUST not succeed.');
        $this->assertNotEmpty($result->errors, 'Error message missing.');
    }

    public function testEmptyContent(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $data = base64_encode('');

        $event = new VerityRequestEvent(Uuid::uuid_create(), 'test-003.txt', null, 'unit_test', $data);

        $this->dispatcher->dispatch($event);
    }

    public function testMissingProfile(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $data = base64_encode('data...');
        $event = new VerityRequestEvent(Uuid::uuid_create(), 'test-004.txt', null, '', $data);

        $this->dispatcher->dispatch($event);
    }

    public function testUnknownProfile(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $data = base64_encode('data...');
        $event = new VerityRequestEvent(Uuid::uuid_create(), 'test-005.txt', null, 'unknown???', $data);

        $this->dispatcher->dispatch($event);
    }
}
