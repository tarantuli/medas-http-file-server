<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Handlers;

use Medas\Core\Attributes\Service;
use Medas\HttpFileServer\{Request, Response, Server};

#[Service]
readonly class DeleteHandler
{
    public function handle(Server $server, Request $request): Response
    {
        $path = $server->directory
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $request->path);

        if (!file_exists($path)) {
            return new Response(404);
        }

        return match (unlink($path)) {
            true => new Response(200),
            false => new Response(500),
        };
    }
}
