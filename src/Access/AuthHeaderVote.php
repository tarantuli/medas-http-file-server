<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Access;

use Medas\Core\Events\BasicVote;
use Medas\HttpFileServer\Request;

class AuthHeaderVote extends BasicVote
{
    public function __construct(
        public Request $request,
    )
    {
    }
}
