<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\FileSystem\LocalFileSystem;
use App\Infrastructure\Response\HttpResponse;
use App\Presentation\Router;

$fileSystem = new LocalFileSystem();
$response = new HttpResponse();
$router = new Router($fileSystem, $response);

$router->route();
