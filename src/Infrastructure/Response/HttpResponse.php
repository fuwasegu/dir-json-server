<?php

namespace App\Infrastructure\Response;

use App\Domain\Response\ResponseInterface;

class HttpResponse implements ResponseInterface
{
    private int $statusCode = 200;
    private string $content = '';

    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        echo $this->content;
    }
}