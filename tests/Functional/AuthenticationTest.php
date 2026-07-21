<?php

declare(strict_types=1);

namespace Medas\HttpFileServerTest\Functional;

use Medas\HttpFileClient\Controller;

class AuthenticationTest extends BaseTestClass
{
    public function testAccessGrantedWithValidToken(): void
    {
        // basic/file.txt is a real, existing mock-up file (see
        // BasicUsageTest) - used here as a known-good reference point, so a
        // false/null result can only mean the request was denied, not that
        // the file happens to not exist.
        self::assertTrue(service(Controller::class)->exists('basic/file.txt', $this->client()));

        self::assertEquals(
            file_get_contents($this->mockUpPath('basic/file.txt')),
            service(Controller::class)->content('basic/file.txt', $this->client())
        );
    }

    public function testAccessDeniedWithoutToken(): void
    {
        self::assertFalse(service(Controller::class)->exists('basic/file.txt', $this->clientWithoutToken()));
        self::assertNull(service(Controller::class)->content('basic/file.txt', $this->clientWithoutToken()));
    }
}
