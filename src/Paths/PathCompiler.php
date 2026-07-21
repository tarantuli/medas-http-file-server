<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Paths;

use Medas\HttpFileServer\{Request, Server};

interface PathCompiler
{
    public function compile(Server $server, Request $request): string;
}
