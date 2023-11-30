<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\AsSingleton;
use Medas\HttpRequestHandler\HttpRequestHandlerPackage;
use Medas\ServiceManager\BasePackage;

class HttpFileServerPackage extends BasePackage
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
