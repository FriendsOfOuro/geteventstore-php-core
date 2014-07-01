<?php

namespace EventStore;

use EventStore\Exception\InvalidWritableEventObjectException;

final class WritableEventCollection implements WritableToStream
{
    private $events = [];

    public function __construct(array $events)
    {
        $this->validateEvents($events);
        $this->events = $events;
    }

    public function toStreamData()
    {
        return array_map(function ($event) {
            return $event->toStreamData();
        }, $this->events);
    }

    private function validateEvents($events)
    {
        foreach ($events as $event) {
            if (!$event instanceof WritableEvent) {
                throw new InvalidWritableEventObjectException();
            }
        }
    }

}
