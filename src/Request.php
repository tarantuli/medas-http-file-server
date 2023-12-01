<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

readonly class Request
{
    public function __construct(
        public string $path,
        public array  $arguments,
        public string $bodyPath,
        public string $bodyEncoding,
    )
    {
    }
}
