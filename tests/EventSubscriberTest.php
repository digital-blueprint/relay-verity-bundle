<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Tests;

use Dbp\Relay\CoreBundle\Exception\ApiError;
use Dbp\Relay\VerityBundle\Event\VerityRequestEvent;
use Dbp\Relay\VerityBundle\Service\DummyAPI;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\File;
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
        $tempDir = sys_get_temp_dir();
        $filePath = tempnam($tempDir, 'dummy_');
        try {
            $data = 'data...';
            file_put_contents($filePath, $data);
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-001.txt',
                null,
                'unit_test',
                new File($filePath),
                'plain/text',
                strlen($data));

            $result = $this->dispatcher->dispatch($event);

            $this->assertTrue($result->valid, 'MUST succeed.');
        } finally {
            unlink($filePath);
        }
    }

    public function testSizeExceeded(): void
    {
        $tempDir = sys_get_temp_dir();
        $filePath = tempnam($tempDir, 'dummy_');
        try {
            DummyAPI::$maxsize = 16;
            $data = 'data.data.data.data.'; // more than 16 chars
            file_put_contents($filePath, $data);
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-002.txt',
                null,
                'unit_test',
                new File($filePath),
                'plain/text',
                strlen($data));

            $result = $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-backend-exception', $exception->getErrorId());
        } finally {
            unlink($filePath);
        }
    }

    public function testEmptyContent(): void
    {
        $tempDir = sys_get_temp_dir();
        $filePath = tempnam($tempDir, 'dummy_');
        try {
            $data = '';
            file_put_contents($filePath, $data);
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-003.txt',
                null,
                'unit_test',
                new File($filePath),
                'plain/text',
                strlen($data));

            $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-file-size-zero', $exception->getErrorId());
        } finally {
            unlink($filePath);
        }
    }

    public function testSizeMismatch(): void
    {
        $tempDir = sys_get_temp_dir();
        $filePath = tempnam($tempDir, 'dummy_');
        try {
            $data = 'data';
            file_put_contents($filePath, $data);
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-004.txt',
                null,
                'unit_test',
                new File($filePath),
                'plain/text',
                1);

            $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-file-size-mismatch', $exception->getErrorId());
        } finally {
            unlink($filePath);
        }
    }

    public function testMissingProfile(): void
    {
        $tempDir = sys_get_temp_dir();
        $filePath = tempnam($tempDir, 'dummy_');
        try {
            $data = 'data...';
            file_put_contents($filePath, $data);
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-005.txt',
                null,
                '',
                new File($filePath),
                'plain/text',
                strlen($data));

            $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-missing-profile', $exception->getErrorId());
        } finally {
            unlink($filePath);
        }
    }

    public function testUnknownProfile(): void
    {
        $tempDir = sys_get_temp_dir();
        $filePath = tempnam($tempDir, 'dummy_');
        try {
            $data = 'data...';
            file_put_contents($filePath, $data);
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-006.txt',
                null,
                'unknown???',
                new File($filePath),
                'plain/text',
                strlen($data));

            $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-missing-profile', $exception->getErrorId());
        } finally {
            unlink($filePath);
        }
    }

    public function testCheckSumCorrect(): void
    {
        $tempDir = sys_get_temp_dir();
        $filePath = tempnam($tempDir, 'dummy_');
        try {
            $data = 'data...';
            file_put_contents($filePath, $data);
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-007.txt',
                hash('sha1', $data),
                'unit_test',
                new File($filePath),
                'plain/text',
                strlen($data));

            $result = $this->dispatcher->dispatch($event);

            $this->assertTrue($result->valid, 'MUST succeed.');
        } finally {
            unlink($filePath);
        }
    }

    public function testCheckSumIncorrect(): void
    {
        $tempDir = sys_get_temp_dir();
        $filePath = tempnam($tempDir, 'dummy_');
        try {
            $data = 'data...';
            file_put_contents($filePath, $data);
            $event = new VerityRequestEvent(Uuid::uuid_create(),
                'test-008.txt',
                hash('sha1', $data.'!!!'),
                'unit_test',
                new File($filePath),
                'plain/text',
                strlen($data));

            $this->dispatcher->dispatch($event);
            $this->fail('Exception should have been thrown.');
        } catch (ApiError $exception) {
            $this->assertEquals('verity:create-report-file-hash-mismatch', $exception->getErrorId());
        } finally {
            unlink($filePath);
        }
    }
}
