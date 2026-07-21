<?php

declare(strict_types=1);

namespace Medas\HttpFileServerTest\Functional;

use Medas\Core\Interfaces\AuthenticationTokenController;
use Medas\HttpFileClient\Client;
use Medas\HttpFileServerTest\MockUps\AuthenticationDataMockUp;
use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    protected function client(): Client
    {
        return new Client(
            url: 'http://localhost/medas/http-file-server/tests/MockUps',
            authorizationHeader: 'Bearer ' . $this->createToken(),
        );
    }

    private function createToken(): string
    {
        // tokenKey/algorithm are bound once in phpunit.bootstrap.php - see
        // the comment there.
        return service(AuthenticationTokenController::class)->create(new AuthenticationDataMockUp());
    }

    protected function clientWithoutToken(): Client
    {
        return new Client(
            url: 'http://localhost/medas/http-file-server/tests/MockUps',
            authorizationHeader: null,
        );
    }

    protected function mockUpPath(string $subPath): string
    {
        return __DIR__ . '/../MockUps/files/' . $subPath;
    }
}
