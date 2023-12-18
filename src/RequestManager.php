<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\{Attributes\Service, Identifier};

#[Service]
readonly class RequestManager
{
    public function compile(array $arguments): Request
    {
        $path = $arguments['path'];

        unset($arguments['path']);

        return new Request(
            $path,
            $arguments,
            'php://input',
            $_SERVER['CONTENT_TYPE'] ?? null,
            $this->gatherHeaders()
        );
    }

    private function gatherHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $keyWords = str_replace('_', ' ', strtolower(substr($key, 5)));
                $identifier = new Identifier(ucwords($keyWords));
                $headers[$identifier->toKebabCase(maintainCase: true)] = $value;
            }
        }

        return $headers;
    }
}
