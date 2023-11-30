<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\FileSystem\FileSystemPackage;
use Medas\HttpFileClient\HttpFileClientPackage;
use Medas\HttpFileServer\HttpFileServerPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

require_once 'vendor/autoload.php';

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        ConfigManagerPackage::instance(),
        ConfigOptionsPackage::instance(),
        FileSystemPackage::instance(),
        HttpFileClientPackage::instance(),
        HttpFileServerPackage::instance(),
    ]);

    return $config;
});
