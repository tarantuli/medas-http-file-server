<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup};

#[Service]
readonly class HttpFileServerGroup implements ConfigGroup
{
    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'http-file-server';
    }
}
