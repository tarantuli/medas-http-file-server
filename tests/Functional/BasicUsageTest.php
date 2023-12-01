<?php

declare(strict_types=1);

namespace Medas\HttpFileServerTest\Functional;

use Medas\HttpFileClient\Controller;

class BasicUsageTest extends BaseTestClass
{
    public function testFetch(): void
    {
        $response = service(Controller::class)->content('basic/file.txt', $this->client());

        self::assertEquals(file_get_contents($this->mockUpPath('basic/file.txt')), $response);
    }

    public function testExists(): void
    {
        $response = service(Controller::class)->exists('basic/file.txt', $this->client());

        self::assertTrue($response);
    }

    public function testSize(): void
    {
        $response = service(Controller::class)->size('basic/file.txt', $this->client());

        self::assertEquals(filesize($this->mockUpPath('basic/file.txt')), $response);
    }

    public function testModificationTime(): void
    {
        $response = service(Controller::class)->modificationTime('basic/file.txt', $this->client());

        self::assertEquals(
            filemtime($this->mockUpPath('basic/file.txt')),
            $response->getTimestamp()
        );
    }

    public function testDoesNotExist(): void
    {
        $response = service(Controller::class)->exists('basic/does-not-exist.txt', $this->client());

        self::assertFalse($response);
    }

    public function testDelete(): void
    {
        $path = $this->mockUpPath('basic/file-to-delete.txt');

        // Set up the test
        if (!file_exists($path)) {
            touch($path);
        }

        // Ensure it exists
        self::assertTrue(service(Controller::class)->exists('basic/file-to-delete.txt', $this->client()));

        // Delete it
        self::assertTrue(service(Controller::class)->delete('basic/file-to-delete.txt', $this->client()));

        // Can't delete it twice
        self::assertFalse(service(Controller::class)->delete('basic/file-to-delete.txt', $this->client()));

        // Ensure it no longer exists
        self::assertFalse(service(Controller::class)->exists('basic/file-to-delete.txt', $this->client()));
    }
}
