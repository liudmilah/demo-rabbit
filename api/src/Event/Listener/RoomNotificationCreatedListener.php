<?php

namespace App\Event\Listener;

use App\Event\Event\RoomNotificationCreatedEvent;
use App\Event\ListenerInterface;
use App\Service\Notifier;

class RoomNotificationCreatedListener implements ListenerInterface
{
    public function __construct(private Notifier $notifier) {}

    public function listen(): array
    {
        return [
            RoomNotificationCreatedEvent::class,
        ];
    }

    public function process(object $event): void
    {
        /** @var RoomNotificationCreatedEvent $event */
        $this->notifier->notifyRoom($event->roomId, [
            'event' => $event->getName(),
            'data' => [
                'message' => $event->message,
            ],
        ]);
    }
}