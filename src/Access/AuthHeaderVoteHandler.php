<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Access;

use Medas\Core\{
    Attributes\EventListener,
    Attributes\Service,
    Interfaces\AuthenticationTokenController
};

#[Service]
readonly class AuthHeaderVoteHandler
{
    public function __construct(
        private AuthenticationTokenController $tokenController,
    )
    {
    }

    #[EventListener]
    public function handleRequest(AuthHeaderVote $authVote): void
    {
        if ($authVote->authorizationHeader === null) {
            // No Authorization header - this handler abstains rather than
            // explicitly allowing or denying. RequestHandler treats an
            // abstained (still-null) vote as denied by default - see the
            // comment there. A reverse proxy or other trusted upstream
            // that strips/replaces the header before requests reach here
            // is expected to be the thing that actually grants access in
            // that case, not this handler.
            return;
        }

        if (!str_starts_with($authVote->authorizationHeader, 'Bearer ')) {
            $this->disallow($authVote);

            return;
        }

        $token = substr($authVote->authorizationHeader, 7);

        if ($this->tokenController->data($token) === null) {
            $this->disallow($authVote);

            return;
        }

        $authVote->allowedAccess = true;
    }

    private function disallow(AuthHeaderVote $authVote): void
    {
        $authVote->allowedAccess = false;
        $authVote->stopPropagation = true;
    }
}
