<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

readonly class Server
{
    public function __construct(
        public string $directory,
    )
    {
    }
}
