<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\{Attributes\Service, Interfaces\EventDispatcher};

#[Service]
readonly class RequestHandler
{
    public function __construct(
        private EventDispatcher        $eventDispatcher,
        private Handlers\DeleteHandler $deleteHandler,
        private Handlers\GetHandler    $getHandler,
        private Handlers\PostHandler   $postHandler,
        private RequestManager         $requestManager,
    )
    {
    }

    public function handle(Server $server, string $method, array $arguments): void
    {
        $request = $this->requestManager->compile($arguments);
        $response = $this->getResponse($request, $method, $server);

        $this->outputResponse($response);
    }

    private function getResponse(Request $request, string $method, Server $server): Response
    {
        $authVote
            = $this->eventDispatcher->dispatch(new Access\AuthHeaderVote($request->headers['Authorization'] ?? null));

        if ($authVote->allowedAccess !== true) {
            // Deny-by-default: this is a strict identity check against
            // true, not a falsy check, so a listener that abstains
            // (leaving allowedAccess at its null default - e.g.,
            // AuthHeaderVoteHandler when no Authorization header is
            // present) is treated the same as an explicit denial. Access
            // is only ever granted by some listener explicitly setting
            // allowedAccess = true.
            $response = new Response(403);
        }
        else {
            try {
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
        }

        return $response;
    }

    private function outputResponse(Response $response): void
    {
        http_response_code($response->code);

        if ($response->type !== null) {
            header(sprintf('Content-type: %s', $response->type));
        }

        echo $response->content;
    }
}
