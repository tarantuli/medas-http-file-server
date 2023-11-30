<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\Attributes\Service;

#[Service]
readonly class GetHandler
{
    public function handle(Server $server, string $path): Response
    {
    }
}
