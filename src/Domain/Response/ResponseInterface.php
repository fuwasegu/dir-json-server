<?php

namespace App\Domain\Response;

interface ResponseInterface
{
    public function setStatusCode(int $code): void;
    public function setContent(string $content): void;
    public function send(): void;
}