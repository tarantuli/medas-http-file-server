<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class Directory implements ConfigOption
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
        return 'directory';
    }

    public function description(): string
    {
        return 'The base directory where files will be stored. 
        
        This value is not used automatically, but you can inject it in your request handler using #[ConfigValue]
        and construct a Server with it.';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        return 'var/storage/http-file-server';
    }
}
