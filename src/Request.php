<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

readonly class Request
{
    public function __construct(
        public string|null $method,
        public string      $path,
        public string|null $returnType,
        public string      $bodyPath,
        public string|null $bodyEncoding,
        public array       $headers,
    )
    {
    }
}
