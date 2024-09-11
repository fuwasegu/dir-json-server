<?php

namespace App\Presentation;

use App\UseCase\GetJsonResponse;
use App\Domain\FileSystem\FileSystemInterface;
use App\Domain\Response\ResponseInterface;

class Router
{
    public function __construct(
        private FileSystemInterface $fileSystem,
        private ResponseInterface $response
    ) {}

    public function route(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        if ($path === '/') {
            $this->showAvailableRoutes();
            return;
        }

        if ($method !== 'GET') {
            $this->response->setStatusCode(405);
            $this->response->setContentType('application/json');
            $this->response->setContent(json_encode([
                "status" => "error",
                "message" => "Method Not Allowed"
            ]));
            $this->response->send();
            return;
        }

        $useCase = new GetJsonResponse($this->fileSystem, $this->response);
        $useCase->execute($path);
        $this->response->setContentType('application/json');
        $this->response->send();
    }

    private function showAvailableRoutes(): void
    {
        $routes = $this->getAvailableRoutes('contents');
        $html = "<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>利用可能なルート</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
        h1 { color: #333; }
        ul { list-style-type: none; padding: 0; }
        li { margin-bottom: 10px; }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>利用可能なルート</h1>
    <ul>";

        foreach ($routes as $route) {
            $html .= "<li><a href='{$route}'>GET {$route}</a></li>";
        }

        $html .= "</ul></body></html>";

        $this->response->setStatusCode(200);
        $this->response->setContentType('text/html');
        $this->response->setContent($html);
        $this->response->send();
    }

    private function getAvailableRoutes(string $dir, string $prefix = ''): array
    {
        $routes = [];
        $contents = $this->fileSystem->getDirectoryContents($dir);

        foreach ($contents as $item) {
            $path = "{$dir}/{$item}";
            if (is_dir($path)) {
                $routes = array_merge($routes, $this->getAvailableRoutes($path, "{$prefix}/{$item}"));
            } elseif ($item === 'response.json') {
                $routes[] = $prefix;
            }
        }

        return $routes;
    }
}