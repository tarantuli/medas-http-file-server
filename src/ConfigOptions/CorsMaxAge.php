<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class CorsMaxAge implements ConfigOption
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
        return 'cors-max-age';
    }

    public function description(): string
    {
        return 'The maximum age (in seconds) that the browser should cache CORS preflight requests';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): int
    {
        return 3600;
    }
}
