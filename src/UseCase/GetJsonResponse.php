<?php

namespace App\UseCase;

use App\Domain\FileSystem\FileSystemInterface;
use App\Domain\Response\ResponseInterface;

class GetJsonResponse
{
    public function __construct(
        private FileSystemInterface $fileSystem,
        private ResponseInterface $response
    ) {}

    public function execute(string $path): void
    {
        $filePath = "contents{$path}/response.json";

        if (!$this->fileSystem->exists($filePath)) {
            $this->response->setStatusCode(404);
            $this->response->setContent(json_encode([
                "status" => "error",
                "message" => "File not found",
                "path" => $filePath
            ]));
            return;
        }

        $content = $this->fileSystem->get($filePath);
        $this->response->setStatusCode(200);
        $this->response->setContent($content);
    }
}