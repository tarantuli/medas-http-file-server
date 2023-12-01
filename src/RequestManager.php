<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\Attributes\Service;

#[Service]
readonly class RequestManager
{
    public function compile(array $arguments): Request
    {
        $path = $arguments['path'];

        unset($arguments['path']);

        return new Request($path, $arguments, 'php://input', $_SERVER['CONTENT_TYPE']);
    }
}
