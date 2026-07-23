<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class CorsAllowedOrigins implements ConfigOption
{
    public function __construct(
        private HttpFileServerGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'cors-allowed-origins';
    }

    public function description(): string
    {
        return 'Comma-separated list of allowed CORS origins. Use * for any origin (not recommended for production)';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        // Default to empty (deny all) for production, can be wildcard "*" in development
        return '';
    }
}
