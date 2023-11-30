<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\Attributes\Service;

#[Service]
readonly class RequestHandler
{
    public function __construct(
        private GetHandler $getHandler,
    )
    {
    }

    public function handle(Server $server, string $method, string $path): Response
    {
        return match (strtoupper($method)) {
            'GET' => $this->getHandler->handle($server, $path),
            default => throw new Exceptions\InvalidRequestMethod($method),
        };
    }
}
