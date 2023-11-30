<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidRequestMethod extends BaseException
{
    public function __construct(string $method)
    {
        parent::__construct($method);
    }

    public function pattern(): string
    {
        return 'invalid request method "%s"';
    }
}
