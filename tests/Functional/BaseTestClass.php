<?php

declare(strict_types=1);

namespace Medas\HttpFileServerTest\Functional;

use Medas\HttpFileClient\Client;
use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    protected function client(): Client
    {
        return new Client('http://localhost/medas/http-file-server/tests/MockUps/');
    }

    protected function mockUpPath(string $subPath): string
    {
        return __DIR__ . '/../MockUps/files/' . $subPath;
    }
}
