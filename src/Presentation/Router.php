<?php

namespace App\Presentation;

use App\Domain\FileSystem\FileSystemInterface;
use App\Domain\Response\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use React\Http\Message\Response;

class Router
{
    private $fileSystem;
    private $response;
    private $rootPath;

    public function __construct(FileSystemInterface $fileSystem, ResponseInterface $response, string $rootPath)
    {
        $this->fileSystem = $fileSystem;
        $this->response = $response;
        $this->rootPath = $rootPath;
    }

    public function route(ServerRequestInterface $request): PsrResponseInterface
    {
        $path = $request->getUri()->getPath();

        if ($path === '/' || $path === '') {
            return $this->handleRootPath();
        }

        $filePath = $this->rootPath . $path . '/response.json';
        if ($this->fileSystem->exists($filePath)) {
            $content = $this->fileSystem->read($filePath);
            return $this->response->json(200, json_decode($content, true));
        }

        // 404エラー時のレスポンスを仕様に合わせて修正
        return $this->response->json(404, [
            'status' => 'error',
            'message' => 'File not found',
            'path' => $filePath
        ]);
    }

    private function handleRootPath(): PsrResponseInterface
    {
        $paths = $this->getAllPaths();
        $html = $this->generateHtml($paths);
        return new Response(
            200,
            ['Content-Type' => 'text/html'],
            $html
        );
    }

    private function getAllPaths(): array
    {
        $paths = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->rootPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'response.json') {
                $relativePath = str_replace($this->rootPath, '', $file->getPath());
                $relativePath = str_replace('\\', '/', $relativePath);
                $paths[] = $relativePath;
            }
        }

        return $paths;
    }

    private function generateHtml(array $paths): string
    {
        $listItems = array_map(function($path) {
            return "<li><a href=\"{$path}\">{$path}</a></li>";
        }, $paths);

        $html = implode("\n", $listItems);

        return <<<HTML
        <!DOCTYPE html>
        <html lang="ja">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>利用可能なAPI一覧</title>
        </head>
        <body>
            <h1>利用可能なAPI一覧</h1>
            <ul>
                {$html}
            </ul>
        </body>
        </html>
        HTML;
    }
}