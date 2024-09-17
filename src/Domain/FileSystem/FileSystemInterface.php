<?php

namespace App\Domain\FileSystem;

interface FileSystemInterface
{
    public function read(string $path): string;
    public function exists(string $path): bool;
}