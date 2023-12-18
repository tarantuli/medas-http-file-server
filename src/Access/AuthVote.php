<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Access;

use Medas\HttpFileServer\Request;
use Psr\EventDispatcher\StoppableEventInterface;

class AuthVote implements StoppableEventInterface
{
    public bool|null $allowedAccess = null;
    public bool $stopPropagation = false;

    public function __construct(
        public Request $request,
    )
    {
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopPropagation;
    }
}
