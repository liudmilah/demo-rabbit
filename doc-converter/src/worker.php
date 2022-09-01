<?php
require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use PhpAmqpLib\Connection\AMQPStreamConnection;

function execute(InputInterface $input, OutputInterface $output) {
    $connection = new AMQPStreamConnection('rabbit-mq', 5672, 'user', 'secret', '/');
    $channel = $connection->channel();

    $exchangeName = 'jobs';
    $channel->exchange_declare($exchangeName, 'direct', false, false, false);

    list($queueName,,) = $channel->queue_declare('', false, false, true, false);

    $channel->queue_bind($queueName, $exchangeName, 'converter');

    $callback = function ($msg) {
        echo ' [x] ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
    };

    $channel->basic_consume($queueName, '', false, true, false, false, $callback);

    while ($channel->is_open()) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();
}

(new SingleCommandApplication())
    ->setName('Document converter')
    ->setCode(execute(...))
    ->run();