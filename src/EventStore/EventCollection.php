<?php

namespace EventStore;

final class EventCollection implements WritableToStream
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
            if (!$event instanceof Event) {
                throw new InvalidEventObjectException();
            }
        }
    }

}
