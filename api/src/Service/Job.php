<?php

namespace App\Service;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Job
{
    private const EXCHANGE = 'jobs';

    public const KEY_EMAIL = 'email';
    public const KEY_CONVERTER = 'converter';

    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(AMQPStreamConnection $connection) {
        $this->connection = $connection;
        $this->channel = $connection->channel();

        $this->initExchange();
        $this->registerDestroyConnectionHandler();
    }

    public function execute(array $payload, string $routingKey): void
    {
        $message = new AMQPMessage(json_encode($payload));

        $this->connection->channel()->basic_publish($message, self::EXCHANGE, $routingKey);
    }

    private function initExchange(): void
    {
        $this->channel->exchange_declare(self::EXCHANGE, 'direct', false, false, false);
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