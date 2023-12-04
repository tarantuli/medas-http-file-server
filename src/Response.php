<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

readonly class Response
{
    public function __construct(
        public int         $code,
        public mixed       $content = '',
        public string|null $type = null,
    )
    {
    }
}
