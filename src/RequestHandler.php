<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\{
    Attributes\PreferredDefault,
    Attributes\Service,
    Events\AllowedAccess,
    Interfaces\EventDispatcher
};

#[Service]
readonly class RequestHandler
{
    public function __construct(
        private EventDispatcher        $eventDispatcher,
        private Handlers\DeleteHandler $deleteHandler,
        private Handlers\GetHandler    $getHandler,
        private Handlers\PostHandler   $postHandler,

        #[PreferredDefault(Paths\HashedTree::class)]
        private Paths\PathCompiler     $pathCompiler,
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
        $authVote = $this->eventDispatcher->dispatch(new Access\AuthHeaderVote($request));

        if ($authVote->allowedAccess !== AllowedAccess::Allowed) {
            // Deny-by-default: BasicVote only stops propagation on an
            // explicit Denied vote (deny-overrides semantics), so
            // Pending/Unauthenticated - an abstained or inconclusive vote.
            // e.g., AuthHeaderVoteHandler when no Authorization header is
            // present - are just as much "not authorized" as an explicit
            // Denied. Only an explicit Allowed vote passes.
            $response = new Response(403);
        }
        else {
            try {
                $response = match (strtoupper($method)) {
                    'GET' => $this->getHandler->handle($server, $request, $this->pathCompiler),
                    'POST' => $this->postHandler->handle($server, $request, $this->pathCompiler),

                    'DELETE'
                        => $this->deleteHandler->handle($server, $request, $this->pathCompiler),

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
