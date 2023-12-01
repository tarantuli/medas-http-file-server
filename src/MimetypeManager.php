<?php

declare(strict_types=1);

namespace Medas\HttpFileServer;

use Medas\Core\Attributes\Service;

#[Service]
readonly class MimetypeManager
{
    public function forFile(string $path): string
    {
        $mimescanner = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($mimescanner, $path);

        finfo_close($mimescanner);

        return $mimetype;
    }
}
