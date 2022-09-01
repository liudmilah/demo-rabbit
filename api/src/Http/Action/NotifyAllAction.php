<?php

namespace App\Http\Action;

use App\Event\Event\PublicNotificationCreatedEvent;
use App\Event\EventDispatcher;
use App\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class NotifyAllAction implements RequestHandlerInterface
{
    public function __construct(private EventDispatcher $dispatcher) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $event = new PublicNotificationCreatedEvent(date('H:i:s'));

        $this->dispatcher->dispatch($event);

        return new JsonResponse([
            'event' => $event->getName(),
            'time' => date('H:i:s')
        ]);
    }
}