<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class PublicPath implements ConfigOption
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
        return 'public-path';
    }

    public function description(): string
    {
        return 'The local path to the public directory. Must end in a slash if not equal to an empty string';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): null
    {
        return null;
    }
}
