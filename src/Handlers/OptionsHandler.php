<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Handlers;

use Medas\Core\{Attributes\ConfigValue, Attributes\Service, Cors\CorsHeaders};
use Medas\HttpFileServer\{ConfigOptions\CorsAllowedOrigins, ConfigOptions\CorsMaxAge, Response};

#[Service]
readonly class OptionsHandler
{
    private const array ALLOWED_METHODS = ['GET', 'POST', 'DELETE', 'OPTIONS'];

    private array $allowedOrigins;

    public function __construct(
        private CorsHeaders $corsHeaders,

        #[ConfigValue(CorsMaxAge::class)]
        private int         $corsMaxAge,

        #[ConfigValue(CorsAllowedOrigins::class)]
        string|array        $allowedOrigins,
    )
    {
        $this->allowedOrigins = is_string($allowedOrigins)
            ? array_map('trim', explode(',', $allowedOrigins))
            : $allowedOrigins;
    }

    public function handle(): Response
    {
        $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $requestedHeaders = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? '';

        $headers = $this->corsHeaders->resolveResponseHeaders(
            $this->allowedOrigins,
            $requestOrigin,
            $this->corsMaxAge,
        );

        $headers = array_merge(
            $headers,
            $this->corsHeaders->resolvePreflightHeaders(self::ALLOWED_METHODS, $requestedHeaders),
        );

        return new Response(204, headers: $headers);
    }

    public function addCorsHeaders(Response $response): Response
    {
        $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';

        $corsHeaders = $this->corsHeaders->resolveResponseHeaders(
            $this->allowedOrigins,
            $requestOrigin,
            $this->corsMaxAge,
        );

        if ($corsHeaders === []) {
            return $response;
        }

        return new Response(
            $response->code,
            $response->content,
            $response->type,
            array_merge($response->headers, $corsHeaders),
        );
    }
}
