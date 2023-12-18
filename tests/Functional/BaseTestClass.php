<?php

declare(strict_types=1);

namespace Medas\HttpFileServerTest\Functional;

use Medas\ApiKeys\{KeyCreator, KeyStoreManager};
use Medas\HttpFileClient\Client;
use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    private const API_NAME = 'http-file-client';

    protected function client(): Client
    {
        $key = service(KeyCreator::class)->create();

        service(KeyStoreManager::class)->storeKey(self::API_NAME, $key);

        return new Client(
            url: 'http://localhost/medas/http-file-server/tests/MockUps',
            authorizationHeader: 'Bearer ' . self::API_NAME . ':' . $key
        );
    }

    protected function mockUpPath(string $subPath): string
    {
        return __DIR__ . '/../MockUps/files/' . $subPath;
    }
}
