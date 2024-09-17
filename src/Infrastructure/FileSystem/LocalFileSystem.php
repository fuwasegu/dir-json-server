<?php

namespace App\Infrastructure\FileSystem;

use App\Domain\FileSystem\FileSystemInterface;

class LocalFileSystem implements FileSystemInterface
{
    public function read(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException("ファイルの読み込みに失敗しました: {$path}");
        }
        return $content;
    }

    public function exists(string $path): bool
    {
        return file_exists($path);
    }
}