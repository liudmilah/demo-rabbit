<?php

namespace App\Http\Action;

use App\Http\JsonResponse;
use App\Job\SendEmailJob;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendEmailAction implements RequestHandlerInterface
{
    public function __construct(private SendEmailJob $job) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = ['to' => 'aaa@aaa.aa'];
        $this->job->execute($data);

        return new JsonResponse([
            'job' => 'SendEmailJob',
            'data' => $data
        ]);
    }
}