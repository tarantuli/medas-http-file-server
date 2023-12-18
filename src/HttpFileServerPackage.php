<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\AsSingleton;
use Medas\FileSystem\FileSystemPackage;
use Medas\HttpClient\HttpClientPackage;
use Medas\ServiceManager\BasePackage;

class HttpFileServerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            FileSystemPackage::instance(),
            HttpClientPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
