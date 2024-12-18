<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Tests;

use Dbp\Relay\CoreBundle\Exception\ApiError;
use Dbp\Relay\VerityBundle\Event\VerityRequestEvent;
use Dbp\Relay\VerityBundle\Service\DummyAPI;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
        $data = 'data...';
        $event = new VerityRequestEvent(Uuid::uuid_create(),
            'test-001.txt',
            null,
            'unit_test',
            $data,
            'plain/text',
            strlen($data));

        $result = $this->dispatcher->dispatch($event);

        $this->assertTrue($result->valid, 'MUST succeed.');
    }

    public function testSizeExceeded(): void
    {
        DummyAPI::$maxsize = 16;
        $data = 'data.data.data.data.'; // more than 16 chars
        try {
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-002.txt',
                null,
                'unit_test',
                $data,
                'plain/text',
                strlen($data));

            $result = $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-backend-exception', $exception->getErrorId());
        }
    }

    public function testEmptyContent(): void
    {
        $data = '';
        try {
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-003.txt',
                null,
                'unit_test',
                $data,
                'plain/text',
                strlen($data));

            $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-file-size-zero', $exception->getErrorId());
        }
    }

    public function testSizeMismatch(): void
    {
        $data = 'data';
        try {
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-004.txt',
                null,
                'unit_test',
                $data,
                'plain/text',
                1);

            $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-file-size-mismatch', $exception->getErrorId());
        }
    }

    public function testMissingProfile(): void
    {
        $data = 'data...';
        try {
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-005.txt',
                null,
                '',
                $data,
                'plain/text',
                strlen($data));

            $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-missing-profile', $exception->getErrorId());
        }
    }

    public function testUnknownProfile(): void
    {
        $data = 'data...';
        try {
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-006.txt',
                null,
                'unknown???',
                $data,
                'plain/text',
                strlen($data));

            $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-missing-profile', $exception->getErrorId());
        }
    }

    public function testCheckSumCorrect(): void
    {
        $data = 'data...';
        $event = new VerityRequestEvent(Uuid::uuid_create(),
            'test-007.txt',
            sha1($data),
            'unit_test',
            $data,
            'plain/text',
            strlen($data));

        $result = $this->dispatcher->dispatch($event);

        $this->assertTrue($result->valid, 'MUST succeed.');
    }

    public function testCheckSumIncorrect(): void
    {
        $data = 'data...';
        try {
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-008.txt',
                sha1($data.'!!!'),
                'unit_test',
                $data,
                'plain/text',
                strlen($data));

            $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-file-hash-mismatch', $exception->getErrorId());
        }
    }
}
