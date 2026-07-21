<?php

declare(strict_types=1);

namespace Medas\HttpFileServerTest\MockUps;

use Medas\Core\Interfaces\AuthenticationData;

readonly class AuthenticationDataMockUp implements AuthenticationData
{
    public function __construct(
        private string $userId = 'test-user',
    )
    {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
