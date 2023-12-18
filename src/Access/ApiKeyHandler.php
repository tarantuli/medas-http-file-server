<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Access;

use Medas\ApiKeys\Validator;
use Medas\Core\Attributes\{EventListener, Service};

#[Service]
readonly class ApiKeyHandler
{
    public function __construct(
        private Validator $validator,
    )
    {
    }

    #[EventListener]
    public function handleRequest(AuthVote $authVote): void
    {
        if (!isset($authVote->request->headers['Authorization'])) {
            // No Authorization header, we pass
            return;
        }

        $header = $authVote->request->headers['Authorization'];

        if (!str_starts_with($header, 'Bearer ')) {
            $this->disAllow($authVote);

            return;
        }

        $token = substr($header, 7);

        if (!str_contains($token, ':')) {
            $this->disAllow($authVote);

            return;
        }

        [$name, $key] = explode(':', $token);

        if (!$this->validator->validate($name, $key)) {
            $this->disAllow($authVote);

            return;
        }

        $authVote->allowedAccess = true;
    }

    private function disAllow(AuthVote $authVote): void
    {
        $authVote->allowedAccess = false;
        $authVote->stopPropagation = true;
    }
}
