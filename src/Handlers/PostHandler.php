<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Handlers;

use Medas\Core\{Attributes\ConfigValue, Attributes\Service, Interfaces\DirectoryCreator};
use Medas\HttpClient\BodyHandler;
use Medas\HttpFileServer\{
    ConfigOptions\PublicPath,
    ConfigOptions\PublicPrefix,
    Exceptions\PublicPathNotSet,
    Paths\PathCompiler,
    Request,
    Response,
    Server
};

#[Service]
readonly class PostHandler
{
    public function __construct(
        private BodyHandler      $bodyHandler,
        private DirectoryCreator $directoryCreator,

        #[ConfigValue(PublicPrefix::class)]
        private string           $publicPrefix,

        #[ConfigValue(PublicPath::class)]
        private string|null      $publicPath,
    )
    {
    }

    public function handle(Server $server, Request $request, PathCompiler $pathCompiler): Response
    {
        if (str_starts_with($request->path, $this->publicPrefix . '/')) {
            if ($this->publicPath === null) {
                throw new PublicPathNotSet();
            }

            $path = $this->publicPath . '/' . $request->path;
        }
        else {
            $path = $pathCompiler->compile($server, $request);
        }

        $this->directoryCreator->create(pathinfo($path, PATHINFO_DIRNAME));

        $rawBody = file_get_contents($request->bodyPath);

        if (false === $rawBody) {
            return new Response(400);
        }

        $body = $this->bodyHandler->parseString($rawBody, $request->bodyEncoding);

        if (false === file_put_contents($path, $body['content'])) {
            return new Response(400);
        }

        if ($body['modificationTime'] !== null) {
            touch($path, $body['modificationTime']);
        }

        return new Response(201);
    }
}
