<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\Events\EventsPackage;
use Medas\FileSystem\FileSystemPackage;
use Medas\HttpFileClient\HttpFileClientPackage;
use Medas\HttpFileServer\HttpFileServerPackage;
use Medas\JsonStorage\{JsonStoragePackage, StorageDirectory};
use Medas\JwtTokens\{JwtAuthTokenController, JwtTokensPackage};
use Medas\ObjectInstantiator\{ObjectInstantiator, ObjectInstantiatorPackage};
use Medas\ServiceManager\{ServiceConfigBuilder, ServiceManager};
use Medas\StorageManager\{StorageManager, StorageManagerPackage};

chdir(__DIR__);

require_once 'vendor/autoload.php';

new ServiceManager(function (): ServiceConfigBuilder {
    $config = new ServiceConfigBuilder(ObjectInstantiator::class);

    $config->addPackages([
        ConfigManagerPackage::instance(),
        ConfigOptionsPackage::instance(),
        EventsPackage::instance(),
        FileSystemPackage::instance(),
        HttpFileClientPackage::instance(),
        HttpFileServerPackage::instance(),
        JsonStoragePackage::instance(),
        JwtTokensPackage::instance(),
        ObjectInstantiatorPackage::instance(),
        StorageManagerPackage::instance(),
    ]);

    // tokenKey must be FIXED, not random: this bootstrap file is required
    // independently by two separate PHP processes that both need to derive
    // the exact same JWT signing key without sharing any runtime state -
    // the PHPUnit test runner itself, and tests/MockUps/server.php (run by
    // Apache per incoming HTTP request during functional tests). A key
    // generated fresh per-process (e.g., random_bytes()) means every token
    // minted by one process fails signature verification in the other.
    // Not a secret that needs protecting - it only ever signs short-lived
    // tokens for this test suite's own mock HTTP requests to itself.
    $config->addManualBinding(
        JwtAuthTokenController::class,
        'tokenKey',
        'http-file-server-tests-fixed-jwt-key-do-not-use-in-production',
    );

    $config->addManualBinding(JwtAuthTokenController::class, 'algorithm', 'HS256');

    return $config;
});

service(StorageManager::class)->add(new StorageDirectory(__DIR__ . '/tests/Storage', 'test-storage'));
