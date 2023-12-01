<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Handlers;

use Medas\Core\Attributes\Service;
use Medas\HttpFileServer\{Request, Response, Server};

#[Service]
readonly class GetHandler
{
    public function handle(Server $server, Request $request): Response
    {
        $path = $server->directory
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $request->path);

        if (!file_exists($path)) {
            return new Response(404);
        }

        return match ($request->arguments['return'] ?? null) {
            'null' => new Response(204),
            'size' => new Response(200, filesize($path)),
            'modificationTime' => new Response(200, filemtime($path)),
            default => new Response(200, file_get_contents($path)),
        };
    }
}
