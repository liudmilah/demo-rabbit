<?php

namespace App\Http\Action;

use App\Http\JsonResponse;
use App\Service\RpcClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class UserInfoAction implements RequestHandlerInterface
{
    public function __construct(private RpcClient $rpc) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->rpc->call(
            ['userId' => '123'],
            RpcClient::KEY_USER_INFO
        );

        return new JsonResponse(['rpcResponse' => json_decode($result)]);
    }
}