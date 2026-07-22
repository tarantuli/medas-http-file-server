<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Handlers;

use Medas\Core\Attributes\Service;
use Medas\Files\MimetypeManager;
use Medas\HttpFileServer\{Paths\PathCompiler, Request, Response, Server};

#[Service]
readonly class GetHandler
{
    public function __construct(
        private MimetypeManager $mimetypeManager,
    )
    {
    }

    public function handle(Server $server, Request $request, PathCompiler $pathCompiler): Response
    {
        $path = $pathCompiler->compile($server, $request);

        if (!file_exists($path)) {
            return new Response(404);
        }

        return match ($request->returnType) {
            'null' => new Response(204),
            'size' => new Response(200, filesize($path)),
            'modificationTime' => new Response(200, filemtime($path)),

            default
                => new Response(
                    200,
                    file_get_contents($path),
                    $this->mimetypeManager->forFilePath($path)
                ),
        };
    }
}
