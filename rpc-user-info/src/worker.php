<?php
require __DIR__.'/../vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use PhpAmqpLib\Connection\AMQPStreamConnection;

function execute(InputInterface $input, OutputInterface $output) {
    $connection = new AMQPStreamConnection('rabbit-mq', 5672, 'user', 'secret', '/');
    $channel = $connection->channel();

    $exchangeName = 'rpc';
    $channel->exchange_declare($exchangeName, 'direct', false, false, false);
    list($queueName,,) = $channel->queue_declare('', false, false, false, false);
    $channel->queue_bind($queueName, $exchangeName, 'user_info');
    $channel->basic_qos(null, 1, null);
    $channel->basic_consume($queueName, '', false, false, false, false, handleRequest(...));

    while ($channel->is_open()) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();
}

function handleRequest($req): void {
    $faker = Faker\Factory::create();
    $data = [
        'name' => $faker->name(),
        'email' => $faker->email(),
        'country' => $faker->country(),
    ];

    $msg = new AMQPMessage(
        json_encode($data),
        ['correlation_id' => $req->get('correlation_id')]
    );

    sleep(3);

    $req->delivery_info['channel']->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );

    $req->ack();
}

(new SingleCommandApplication())
    ->setName('Get user info')
    ->setCode(execute(...))
    ->run();