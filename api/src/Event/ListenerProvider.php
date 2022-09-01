<?php

declare(strict_types=1);

namespace App\Event;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

final class ListenerProvider implements ListenerProviderInterface
{
    public array $listeners = [];

    public function __construct(array $listenersClasses, ContainerInterface $container)
    {
        foreach ($listenersClasses as $listenerClass) {
            $listener = $container->get($listenerClass);
            if ($listener instanceof ListenerInterface) {
                foreach ($listener->listen() as $eventClass) {
                    $this->listeners[] = new EventListener($eventClass, [$listener, 'process']);
                }
            }
        }
    }

    public function getListenersForEvent(object $event): iterable
    {
        $result = [];
        foreach ($this->listeners as $listener) {
            if ($event instanceof $listener->eventClass) {
                $result[] = $listener->listener;
            }
        }
        return $result;
    }
}