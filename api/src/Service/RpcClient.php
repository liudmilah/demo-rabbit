<?php

namespace App\Service;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RpcClient
{
    private const EXCHANGE = 'rpc';

    public const KEY_USER_INFO = 'user_info';

    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;
    private string $callbackQueue;
    private string $correlationId = '';
    private mixed $response = null;

    public function __construct(AMQPStreamConnection $connection) {
        $this->connection = $connection;
        $this->channel = $connection->channel();

        $this->initExchange();
        $this->initCallbackQueue();
        $this->registerDestroyConnectionHandler();
    }

    public function call(array $data, string $routingKey)
    {
        $this->correlationId = uniqid();

        $msg = new AMQPMessage(
            json_encode($data),
            [
                'correlation_id' => $this->correlationId,
                'reply_to' => $this->callbackQueue
            ]
        );

        $this->channel->basic_publish($msg, self::EXCHANGE, $routingKey);

        while (!$this->response) {
            $this->channel->wait();
        }

        return $this->response;
    }

    private function initExchange(): void
    {
        $this->channel->exchange_declare(self::EXCHANGE, 'direct', false, false, false);
    }

    private function initCallbackQueue(): void
    {
        list($this->callbackQueue,,) = $this->channel->queue_declare(
            "",
            false,
            false,
            true,
            false
        );
        $this->channel->basic_consume(
            $this->callbackQueue,
            '',
            false,
            true,
            false,
            false,
            $this->handleRpcResponse(...)
        );
    }

    private function handleRpcResponse($response): void
    {
        if ($response->get('correlation_id') == $this->correlationId) {
            $this->response = $response->body;
        }
    }

    private function registerDestroyConnectionHandler(): void
    {
        register_shutdown_function(
            function (AMQPChannel $channel, AMQPStreamConnection $connection) {
                $channel->close();
                $connection->close();
            },
            $this->channel,
            $this->connection
        );
    }
}