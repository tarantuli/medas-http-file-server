<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\{Attributes\ConfigValue, Attributes\Service, Identifier};

#[Service]
readonly class RequestManager
{
    public function __construct(
        #[ConfigValue(ConfigOptions\ScriptNameSuffix::class)]
        private string $scriptNameSuffix,
    )
    {
    }

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
        // SCRIPT_NAME is a standard SAPI variable, always present, and for
        // this front-controller layout is always <mount><suffix>, where <suffix> is
        // a known constant. Deriving the mount point from it is immune to Apache's REDIRECT_
        // prefixing quirks. (Whether a custom env var like BASE keeps its
        // name or gets prefixed depends on exactly how many internal
        // rewrite passes happened, which isn't reliably stable.)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        $mount = preg_replace(
            '#' . preg_quote($this->scriptNameSuffix, '#') . '$#',
            '',
            $scriptName
        );

        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        $path = ($mount !== '' && str_starts_with($requestPath, $mount))
            ? substr($requestPath, strlen($mount))
            : $requestPath;

        return ltrim($path, '/');
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
