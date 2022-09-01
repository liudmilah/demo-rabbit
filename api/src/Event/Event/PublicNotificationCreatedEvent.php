<?php

namespace App\Event\Event;

use App\Event\EventInterface;

class PublicNotificationCreatedEvent implements EventInterface
{
    public function __construct(
        public string $message,
    ) {}

    public function getName(): string
    {
        return 'PUBLIC_NOTIFICATION_CREATED';
    }
}