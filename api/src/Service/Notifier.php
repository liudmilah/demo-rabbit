<?php

namespace App\Service;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Notifier
{
    private const EXCHANGE = 'notifications';

    private const KEY_ROOM = 'room.*';
    private const KEY_ALL = 'all';

    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(AMQPStreamConnection $connection) {
        $this->connection = $connection;
        $this->channel = $connection->channel();

        $this->initExchange();
        $this->registerDestroyConnectionHandler();
    }

    public function notifyRoom(string $roomId, array $payload): void
    {
        $this->notify($payload, str_replace('*', $roomId, self::KEY_ROOM));
    }

    public function notifyAll(array $payload): void
    {
        $this->notify($payload, self::KEY_ALL);
    }

    private function notify(array $payload, string $routingKey): void
    {
        $message = new AMQPMessage(json_encode($payload));

        $this->connection->channel()->basic_publish($message, self::EXCHANGE, $routingKey);
    }

    private function initExchange(): void
    {
        $this->channel->exchange_declare(self::EXCHANGE, 'topic', false, false, false);
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