<?php

declare(strict_types=1);

namespace App\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(private ListenerProviderInterface $listeners)
    {
    }

    public function dispatch(object $event): object
    {
        /** @var callable $listener */
        foreach ($this->listeners->getListenersForEvent($event) as $listener) {
            $listener($event);
        }
        return $event;
    }
}