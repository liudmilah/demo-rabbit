<?php

namespace App\Event\Event;

use App\Event\EventInterface;

class RoomNotificationCreatedEvent implements EventInterface
{
    public function __construct(
        public string $roomId,
        public string $message,
    ) {}

    public function getName(): string
    {
        return 'ROOM_NOTIFICATION_CREATED';
    }
}