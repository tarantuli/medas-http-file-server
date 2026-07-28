<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class ScriptNameSuffix implements ConfigOption
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
        return 'script-name-suffix';
    }

    public function description(): string
    {
        return 'The suffix of SCRIPT_NAME (e.g. /public/index.php) stripped from the end to determine '
            . 'the application\'s mount point relative to the domain root. Change this if the front '
            . 'controller doesn\'t live at the default public/index.php location.';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        return '/public/index.php';
    }
}
