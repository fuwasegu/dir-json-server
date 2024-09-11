<?php

namespace App\Infrastructure\Response;

use App\Domain\Response\ResponseInterface;

class HttpResponse implements ResponseInterface
{
    private int $statusCode = 200;
    private string $content = '';
    private string $contentType = 'application/json';

    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header("Content-Type: {$this->contentType}");
        echo $this->content;
    }
}