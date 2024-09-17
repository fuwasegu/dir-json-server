<?php

namespace App\Command;

use App\Infrastructure\FileSystem\LocalFileSystem;
use App\Infrastructure\Response\HttpResponse;
use App\Presentation\Router;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Symfony\Component\Yaml\Yaml;

class ServeCommand
{
    public function run(): void
    {
        $host = '127.0.0.1';

        $config = $this->loadConfig();
        $rootPath = $config['root_path'] ?? 'contents';
        $port = $config['port'] ?? 8000;

        $server = new HttpServer(function (\Psr\Http\Message\ServerRequestInterface $request) use ($rootPath) {
            $fileSystem = new LocalFileSystem();
            $response = new HttpResponse();
            $router = new Router($fileSystem, $response, $rootPath);

            return $router->route($request);
        });

        echo "サーバーを起動しています。http://{$host}:{$port}\n";
        echo "ルートパス: {$rootPath}\n";

        $socket = new SocketServer("{$host}:{$port}");
        $server->listen($socket);

        \React\EventLoop\Loop::run();
    }

    private function loadConfig(): array
    {
        $configFile = $this->findConfigFile();
        if ($configFile && file_exists($configFile)) {
            return Yaml::parseFile($configFile);
        }
        return [];
    }

    private function findConfigFile(): ?string
    {
        $currentDir = getcwd();
        while ($currentDir !== '/') {
            $configPath = $currentDir . '/dir-json.yaml';
            if (file_exists($configPath)) {
                return $configPath;
            }
            $currentDir = dirname($currentDir);
        }
        return null;
    }
}