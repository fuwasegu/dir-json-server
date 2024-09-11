<?php

namespace App\Domain\FileSystem;

interface FileSystemInterface
{
    public function exists(string $path): bool;
    public function get(string $path): string;
    public function getDirectoryContents(string $path): array;
}