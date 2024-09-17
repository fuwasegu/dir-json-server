<?php

namespace App\Infrastructure\Response;

use App\Domain\Response\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use React\Http\Message\Response;

class HttpResponse implements ResponseInterface
{
    public function json(int $status, array $data): PsrResponseInterface
    {
        return new Response(
            $status,
            ['Content-Type' => 'application/json'],
            json_encode($data)
        );
    }
}