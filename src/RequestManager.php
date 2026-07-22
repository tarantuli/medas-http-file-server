<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\{Attributes\Service, Identifier};

#[Service]
readonly class RequestManager
{
    public function compile(): Request
    {
        return new Request(
            $_SERVER['REQUEST_METHOD'] ?? null,
            $this->determinePath(),
            $_GET['return'] ?? null,
            'php://input',
            $_SERVER['CONTENT_TYPE'] ?? null,
            $this->gatherHeaders()
        );
    }

    private function determinePath(): string
    {
        $pathOffset = array_key_exists('REDIRECT_BASE', $_SERVER)
            ? strlen($_SERVER['REDIRECT_BASE'])
            : 0;

        $path = substr($_SERVER['REQUEST_URI'], $pathOffset + 1);

        if (false !== $pos = strpos($path, '?')) {
            $path = substr($path, 0, $pos);
        }

        return $path;
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
