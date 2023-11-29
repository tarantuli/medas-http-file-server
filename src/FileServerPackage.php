<?php

declare(strict_types=1);

namespace Medas\FileServer;

use Medas\Core\AsSingleton;
use Medas\HttpRequestHandler\HttpRequestHandlerPackage;
use Medas\ServiceManager\BasePackage;

class FileServerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            HttpRequestHandlerPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
