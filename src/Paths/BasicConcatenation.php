<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Paths;

use Medas\Core\Attributes\Service;
use Medas\HttpFileServer\{Request, Server};

#[Service]
readonly class BasicConcatenation implements PathCompiler
{
    public function compile(Server $server, Request $request): string
    {
        return $server->directory
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $request->path);
    }
}
