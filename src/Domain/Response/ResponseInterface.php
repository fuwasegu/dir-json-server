<?php

namespace App\Domain\Response;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface
{
    public function json(int $status, array $data): PsrResponseInterface;
}