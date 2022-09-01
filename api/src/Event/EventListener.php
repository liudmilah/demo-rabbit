<?php

namespace App\Event;

class EventListener
{
    /** @var callable $listener */
    public function __construct(public string $eventClass, public $listener) {}
}