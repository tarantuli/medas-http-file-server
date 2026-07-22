<?php

declare(strict_types=1);

namespace Medas\HttpFileServerTest\MockUps;

use Medas\HttpFileServer\{RequestHandler, Server};

require_once __DIR__ . '/../../phpunit.bootstrap.php';

$server = new Server(__DIR__ . DIRECTORY_SEPARATOR . 'files');

service(RequestHandler::class)->handle($server);
