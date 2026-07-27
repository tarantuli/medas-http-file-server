<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class PublicPrefix implements ConfigOption
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
        return 'public-prefix';
    }

    public function description(): string
    {
        return 'The path prefix of public files';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        return 'public';
    }
}
