<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Handlers;

use Medas\Core\{Attributes\Service, Interfaces\DirectoryCreator};
use Medas\HttpClient\BodyHandler;
use Medas\HttpFileServer\{Paths\PathCompiler, Request, Response, Server};

#[Service]
readonly class PostHandler
{
    public function __construct(
        private BodyHandler      $bodyHandler,
        private DirectoryCreator $directoryCreator,
    )
    {
    }

    public function handle(Server $server, Request $request, PathCompiler $pathCompiler): Response
    {
        $path = $pathCompiler->compile($server, $request);

        $this->directoryCreator->create(pathinfo($path, PATHINFO_DIRNAME));

        $body = $this->bodyHandler->parseString(
            file_get_contents($request->bodyPath),
            $request->bodyEncoding
        );

        if (false === file_put_contents($path, $body['content'])) {
            return new Response(400);
        }

        if ($body['modificationTime'] !== null) {
            touch($path, $body['modificationTime']);
        }

        return new Response(201);
    }
}
