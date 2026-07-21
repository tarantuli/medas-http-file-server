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

        // Injected here, once, and threaded through to each handler's
        // handle() call below - deliberately NOT constructor-injected into
        // GetHandler/PostHandler/DeleteHandler individually. There are
        // (and are meant to be) multiple PathCompiler implementations
        // (BasicConcatenation, HashedTree), so an unattributed constructor
        // parameter on each handler would be ambiguous and fail to
        // resolve at all. (ServiceFinderByType throws
        // MultipleImplementorsFoundForParameter for a type with more than
        // one bound implementor and no override.) Giving each handler its
        // own #[PreferredDefault(...)] would resolve that. However, then
        // overriding the compiler means updating every handler's binding
        // in lockstep - miss one, and GET/POST/DELETE silently disagree on
        // where a given request path lives on disk, which is exactly the
        // kind of bug that only shows up as "the file I just uploaded
        // can't be found" days later. Resolving it once here means there
        // is exactly one place to override and no way for the handlers to
        // end up with different compilers.
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
            catch (Exceptions\InvalidPath $exception) {
                // The client's fault, not the server's - a malformed or
                // unsafe path (e.g., traversal) should read as a bad
                // request, not an internal error.
                $response = new Response(400, $exception->getMessage());
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
