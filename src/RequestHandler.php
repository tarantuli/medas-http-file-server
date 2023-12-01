<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\Attributes\Service;

#[Service]
readonly class RequestHandler
{
    public function __construct(
        private Handlers\DeleteHandler $deleteHandler,
        private Handlers\GetHandler    $getHandler,
        private RequestManager         $requestManager,
        private Handlers\PostHandler   $postHandler,
    )
    {
    }

    public function handle(Server $server, string $method, array $arguments): void
    {
        try {
            $request = $this->requestManager->compile($arguments);
            $response = match (strtoupper($method)) {
                'GET' => $this->getHandler->handle($server, $request),
                'POST' => $this->postHandler->handle($server, $request),
                'DELETE' => $this->deleteHandler->handle($server, $request),
                default => new Response(400),
            };
        }
        catch (\Exception $exception) {
            $response = new Response(500, $exception->getMessage());
        }

        http_response_code($response->code);

        echo $response->content;
    }
}
