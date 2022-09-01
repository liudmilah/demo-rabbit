<?php

namespace App\Http\Action;

use App\Http\JsonResponse;
use App\Job\ConvertDocJob;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ConvertDocAction implements RequestHandlerInterface
{
    public function __construct(private ConvertDocJob $job) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = ['from' => 'txt', 'to' => 'pdf'];
        $this->job->execute($data);

        return new JsonResponse([
            'job' => 'ConvertDocJob',
            'data' => $data
        ]);
    }
}