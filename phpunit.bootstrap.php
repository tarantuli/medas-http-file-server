<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\Events\EventsPackage;
use Medas\FileSystem\FileSystemPackage;
use Medas\HttpFileClient\HttpFileClientPackage;
use Medas\HttpFileServer\HttpFileServerPackage;
use Medas\JsonStorage\JsonStoragePackage;
use Medas\JsonStorage\StorageDirectory;
use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\ObjectInstantiator\ObjectInstantiatorPackage;
use Medas\StorageManager\StorageManager;
use Medas\StorageManager\StorageManagerPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

require_once 'vendor/autoload.php';

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig(ObjectInstantiator::class);

    $config->addPackages([
        ConfigManagerPackage::instance(),
        ConfigOptionsPackage::instance(),
        EventsPackage::instance(),
        FileSystemPackage::instance(),
        HttpFileClientPackage::instance(),
        HttpFileServerPackage::instance(),
        JsonStoragePackage::instance(),
        ObjectInstantiatorPackage::instance(),
        StorageManagerPackage::instance(),
    ]);

    return $config;
});

service(StorageManager::class)->add(
    new StorageDirectory(__DIR__ . '/tests/Storage', 'test-storage')
);
