<?php

declare(strict_types=1);

namespace Medas\HttpFileServerTest\Functional;

use Medas\HttpFileClient\Controller;

class StoreTest extends BaseTestClass
{
    public function testBasicStorage(): void
    {
        $path = $this->mockUpPath('basic/file-to-store.txt');
        $content = "This is content \xF0\xA4\xAD\xA2\xF0\xA4\xAD\xA2\xF0\xA4\xAD :-)";

        // Set up the test
        if (file_exists($path)) {
            unlink($path);
        }

        $response = service(Controller::class)->store(
            'basic/file-to-store.txt',
            $content,
            client: $this->client()
        );

        self::assertTrue($response);
        self::assertEquals($content, file_get_contents($path));

        // Clean up the test
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
