<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\{AsSingleton, BasePackage};
use Medas\FileSystem\FileSystemPackage;
use Medas\Files\FilesPackage;
use Medas\HttpClient\HttpClientPackage;

class HttpFileServerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            FileSystemPackage::instance(),
            FilesPackage::instance(),
            HttpClientPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
