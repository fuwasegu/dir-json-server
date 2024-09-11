<?php

namespace App\Infrastructure\FileSystem;

use App\Domain\FileSystem\FileSystemInterface;

class LocalFileSystem implements FileSystemInterface
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function get(string $path): string
    {
        return file_get_contents($path);
    }

    public function getDirectoryContents(string $path): array
    {
        return array_diff(scandir($path), ['.', '..']);
    }
}