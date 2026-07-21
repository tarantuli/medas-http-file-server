<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidPath extends BaseException
{
    public function __construct(string $path)
    {
        parent::__construct($path);
    }

    public function pattern(): string
    {
        return 'Invalid or unsafe request path: %s';
    }
}
