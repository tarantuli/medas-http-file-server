<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Paths;

use Medas\Core\Attributes\Service;
use Medas\HttpFileServer\{Request, Server};

#[Service]
readonly class HashedTree implements PathCompiler
{
    public function compile(Server $server, Request $request): string
    {
        $hash = md5($request->path);

        return $server->directory
            . DIRECTORY_SEPARATOR
            . substr($hash, 0, 2)
            . DIRECTORY_SEPARATOR
            . substr($hash, 2, 2)
            . DIRECTORY_SEPARATOR
            . substr($hash, 4);
    }
}
