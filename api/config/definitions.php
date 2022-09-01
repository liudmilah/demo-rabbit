<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Container\ContainerInterface;
use App\Event\ListenerProvider;
use Psr\EventDispatcher\ListenerProviderInterface;
use App\Event\Listener\PublicNotificationCreatedListener;
use App\Event\Listener\RoomNotificationCreatedListener;

return [

    AMQPStreamConnection::class => function (ContainerInterface $container) {
        $config = $container->get('config')['amqp'];
        return new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['username'],
            $config['password'],
            $config['vhost']
        );
    },

    ListenerProviderInterface::class => static function (ContainerInterface $container): ListenerProvider {
        $config = $container->get('config')['events'];
        return new ListenerProvider($config['listeners'], $container);
    },

    'config' => [
        'amqp' => [
            'host' => 'rabbit-mq',
            'port' => 5672,
            'username' => 'user',
            'password' => 'secret',
            'vhost' => '/',
        ],
        'events' => [
            'listeners' => [
                PublicNotificationCreatedListener::class,
                RoomNotificationCreatedListener::class,
            ],
        ],
    ]
];