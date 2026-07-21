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
        // the leaf, rather than hashing it away entirely - GetHandler
        // Keep the original filename (and therefore its extension) as
        $hash = md5($request->path);

        return $server->directory
            . DIRECTORY_SEPARATOR
            . substr($hash, 0, 2)
            . DIRECTORY_SEPARATOR
            . substr($hash, 2, 2)
            . DIRECTORY_SEPARATOR// relies on MimetypeManager::forFilePath() detecting type from
            // the extension, which silently breaks for every file if the
            // leaf is just hash bytes with nothing to detect.
            . basename($request->path);
    }
}
