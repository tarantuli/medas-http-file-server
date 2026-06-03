# medas-http-file-server

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

The server-side counterpart to `medas-http-file-client`. It exposes a directory of files over HTTP with a simple REST API, authenticated via the `medas-api-keys` `name:key` bearer token scheme.

`RequestHandler` is the entry point: it dispatches an `AuthHeaderVote` to validate the `Authorization` header, then routes to `GetHandler`, `PostHandler`, or `DeleteHandler` based on the HTTP method. All file paths are resolved relative to the `Server::$directory` root.

**API contract:**

| Method | `?return`          | Response             | Description                                           |
|--------|--------------------|----------------------|-------------------------------------------------------|
| GET    | _(absent)_         | 200 + file content   | Return file content with detected MIME type           |
| GET    | `null`             | 204                  | Check existence (204 = exists, 404 = not found)       |
| GET    | `size`             | 200 + file size      | Return file size in bytes                             |
| GET    | `modificationTime` | 200 + Unix timestamp | Return last-modified timestamp                        |
| POST   | —                  | 201                  | Store file (JSON body: `{content, modificationTime}`) |
| DELETE | —                  | 200                  | Delete file                                           |

**Authentication:** every request must include `Authorization: Bearer name:key`. The bearer token is split on `:` and validated via `ApiKeys\Validator`. Requests without a valid token receive a `403`. Requests for non-existent files receive `404`. Exceptions during file operations produce `500`.

`AuthHeaderVoteHandler` listens to `AuthHeaderVote` events. If no `Authorization` header is present, access is passed (for use in development or behind a trusted reverse proxy). If a header is present and fails validation, `stopPropagation` is set so no other listener can override the denial.

## Usage

### Package developer context

Register the package and call `RequestHandler::handle()` from your file-server entry point:

```php
use Medas\HttpFileServer\HttpFileServerPackage;

HttpFileServerPackage::instance();
```

**Minimal `index.php` for a file server:**

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Medas\ServiceManager\{ServiceConfig, ServiceManager};
use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\HttpFileServer\{HttpFileServerPackage, RequestHandler, Server};
// ... other required packages

chdir(__DIR__ . '/..');

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig(objectInstantiatorClass: ObjectInstantiator::class);

    $config->addPackages([
        // ... storage and config packages
        HttpFileServerPackage::instance(),
    ]);

    return $config;
});

$server = new Server(directory: __DIR__ . '/../../storage');

service(RequestHandler::class)->handle(
    server: $server,
    method: $_SERVER['REQUEST_METHOD'],
    arguments: $_REQUEST,
);
```

**`Server` configuration:**

```php
use Medas\HttpFileServer\Server;

// All file paths are resolved relative to this directory
$server = new Server(directory: '/var/www/file-storage');
```

**Issuing an API key for a client:**

```bash
# Create an API key for a named client
php bin/medas api-keys:create-key my-client
# API key: 3f8a2c...

# The client uses it as: Authorization: Bearer my-client:3f8a2c...
```

**Using with `medas-http-file-client`:**

The file server implements exactly the HTTP contract that `medas-http-file-client`'s `Controller` expects. To connect them:

```php
use Medas\HttpFileClient\{Client, ClientManager};

$clientManager->register(new Client(
    url: 'https://files.example.com',
    authorizationHeader: 'Bearer my-client:3f8a2c...',
));
```

### Backend user context

**Deploying the file server** — the file server is a standalone PHP application with its own `index.php` entry point and web server configuration. Route all requests to `index.php`:

```apache
# Apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

```nginx
# Nginx
location / {
    try_files $uri /index.php?$query_string;
}
```

The request path is taken from `$_REQUEST` (passed as `$arguments` to `RequestHandler::handle()`), so the path must be available as a query parameter or POST field named `path`.

**No Authorization header** — by default `AuthHeaderVoteHandler` allows requests with no `Authorization` header (access is not explicitly denied). To require authentication for all requests, add a higher-priority `#[EventListener]` that denies when `$authVote->allowedAccess` is still `null` after all handlers have run, or run the file server behind a reverse proxy that enforces authentication.

**Storage directory** — ensure the directory configured in `Server::$directory` is writable by the PHP process and is not web-accessible directly (only the file server's own `index.php` should be publicly reachable).
