<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Paths;

use Medas\Core\Attributes\Service;
use Medas\HttpFileServer\{Exceptions\InvalidPath, Request, Server};

#[Service]
readonly class BasicConcatenation implements PathCompiler
{
    public function compile(Server $server, Request $request): string
    {
        $this->assertSafe($request->path);

        return $server->directory
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $request->path);
    }

    // Deliberately strict, not just "block '..'": since this
    // implementation keeps the request path recognizable on disk (unlike
    // HashedTree, where hashing incidentally makes traversal harmless),
    // it's the one PathCompiler that actually needs to guarantee safety
    // itself. Every segment must look like an ordinary, single-level
    // descending path component - no traversal, no absolute paths, no
    // drive letters, no backslashes, no null bytes, nothing exotic. This
    // rejects some legitimate-but-unusual filenames (spaces, Unicode,
    // leading dots) in exchange for not having to reason about every way
    // a "normal-looking" path could still be dangerous.
    private function assertSafe(string $path): void
    {
        if ($path === '') {
            throw new InvalidPath($path);
        }

        $segments = explode('/', $path);

        foreach ($segments as $segment) {
            if (!preg_match('/^[A-Za-z0-9_-]+(?:\.[A-Za-z0-9_-]+)*$/', $segment)) {
                throw new InvalidPath($path);
            }
        }
    }
}
