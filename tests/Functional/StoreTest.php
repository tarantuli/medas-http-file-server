<?php

declare(strict_types=1);

namespace Medas\HttpFileServerTest\Functional;

use Medas\HttpFileClient\Controller;

class StoreTest extends BaseTestClass
{
    public function testBasicStorage(): void
    {
        $path = $this->mockUpPath('basic/file-to-store.txt');

        // Set up the test
        if (file_exists($path)) {
            unlink($path);
        }

        $response = service(Controller::class)->store(
            'basic/file-to-store.txt',
            'This is content.',
            client: $this->client()
        );

        self::assertTrue($response);
    }
}
