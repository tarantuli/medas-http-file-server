# medas-http-file-server

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

The server-side counterpart to `medas-http-file-client`. It exposes a directory of files over HTTP with a simple REST API, authenticated via a bearer token validated through `Medas\Core\Interfaces\AuthenticationTokenController` - the same abstraction used for session/user authentication elsewhere in the framework, rather than a dedicated API-key scheme.

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

**Authentication:** every request must include `Authorization: Bearer <token>`, where `<token>` is validated via `AuthenticationTokenController::data()`. This package declares no hard dependency on any concrete token controller – it only requires the interface (already part of `medas-core`) and expects the consuming application to have one bound, e.g. `medas-jwt-tokens`' `JwtAuthTokenController`, which `AuthHeaderVoteHandler` falls back to via `#[PreferredDefault]` if nothing else is bound. Requests without a valid token receive a `403`. Requests for non-existent files receive `404`. Exceptions during file operations produce `500`.

`AuthHeaderVoteHandler` listens to `AuthHeaderVote` events. If no `Authorization` header is present, or if the header fails validation, access is denied (`allowedAccess` is only ever explicitly set to `true` by a successful bearer token check) - `RequestHandler` denies by default and only grants access when some listener explicitly says so. If a header is present and fails validation, `stopPropagation` is also set so no other listener can override the denial.

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

use Medas\ServiceManager\{ServiceConfigBuilder, ServiceManager};
use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\HttpFileServer\{HttpFileServerPackage, RequestHandler, Server};
// ... other required packages

chdir(__DIR__ . '/..');

new ServiceManager(function (): ServiceConfigBuilder {
    $config = new ServiceConfigBuilder(objectInstantiatorClass: ObjectInstantiator::class);

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

**Issuing a token for a client:**

A token is anything your bound `AuthenticationTokenController` implementation will accept back from `data()`. With `medas-jwt-tokens` as the default:

```php
use Medas\Core\Interfaces\AuthenticationTokenController;

$token = service(AuthenticationTokenController::class)->create($someAuthenticationData);
// The client uses it as: Authorization: Bearer <token>
```

`$someAuthenticationData` is any `Medas\Core\Interfaces\AuthenticationData` implementation representing the client this token is for.

**Using with `medas-http-file-client`:**

The file server implements exactly the HTTP contract that `medas-http-file-client`'s `Controller` expects. To connect them:

```php
use Medas\HttpFileClient\{Client, ClientManager};

$clientManager->register(new Client(
    url: 'https://files.example.com',
    authorizationHeader: 'Bearer ' . $token,
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

**No Authorization header** — by default `AuthHeaderVoteHandler` abstains (neither allows nor denies) when no `Authorization` header is present, but `RequestHandler` denies by default whenever nothing has explicitly granted access, so a missing header results in a `403` unless something else in the chain explicitly sets `allowedAccess = true`. To allow specific unauthenticated access, add a higher-priority `#[EventListener]` that explicitly sets `$authVote->allowedAccess = true` for whatever case should be exempt, rather than relying on the absence of denial.

**Storage directory** — ensure the directory configured in `Server::$directory` is writable by the PHP process and is not web-accessible directly (only the file server's own `index.php` should be publicly reachable).
