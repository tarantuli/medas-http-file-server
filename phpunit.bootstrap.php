<?php

declare(strict_types=1);

use Medas\FileServer\FileServerPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        FileServerPackage::instance(),
    ]);

    return $config;
});
