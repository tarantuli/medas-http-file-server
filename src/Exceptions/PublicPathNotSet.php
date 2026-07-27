<?php

declare(strict_types=1);

namespace Medas\HttpFileServer\Exceptions;

use Medas\Core\Exceptions\BaseException;

class PublicPathNotSet extends BaseException
{
    public function pattern(): string
    {
        return 'the local path to the public directory is not set';
    }
}
