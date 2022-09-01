<?php

namespace App\Event\Listener;

use App\Event\Event\PublicNotificationCreatedEvent;
use App\Event\ListenerInterface;
use App\Service\Notifier;

class PublicNotificationCreatedListener implements ListenerInterface
{
    public function __construct(private Notifier $notifier) {}

    public function listen(): array
    {
        return [
            PublicNotificationCreatedEvent::class,
        ];
    }

    public function process(object $event): void
    {
        /** @var PublicNotificationCreatedEvent $event */
        $this->notifier->notifyAll([
            'event' => $event->getName(),
            'data' => [
                'message' => $event->message,
            ],
        ]);
    }
}