<?php

namespace App\Event;

interface ListenerInterface
{
    public function listen(): array;
    public function process(object $event): void;
}