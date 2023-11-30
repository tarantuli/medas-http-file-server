<?php

declare(strict_types=1);

namespace Medas\HttpFileServerTest\Functional;

use Medas\HttpFileClient\Controller;

class BasicUsageTest extends BaseTestClass
{
    public function testFetch(): void
    {
        $content = service(Controller::class)->content('basic/file.txt', $this->client());

        diedump($content);

        self::assertEquals(
            file_get_contents(__DIR__ . '/../MockUps/files/basic/file.txt'),
            $content
        );
    }
}
