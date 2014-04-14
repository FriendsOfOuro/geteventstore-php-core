<?php

namespace EventStore;

class StreamEventsSlice
{
    private $status;
    private $fromEventNumber;
    private $readDirection;
    private $events;
    private $nextEventNumber;
    private $lastEventNumber;

    function __construct($status, $fromEventNumber, $readDirection, $events, $nextEventNumber, $lastEventNumber)
    {
        $this->status = $status;
        $this->events = $events;
        $this->fromEventNumber = $fromEventNumber;
        $this->lastEventNumber = $lastEventNumber;
        $this->nextEventNumber = $nextEventNumber;
        $this->readDirection = $readDirection;
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return mixed
     */
    public function getFromEventNumber()
    {
        return $this->fromEventNumber;
    }

    /**
     * @return mixed
     */
    public function getLastEventNumber()
    {
        return $this->lastEventNumber;
    }

    /**
     * @return mixed
     */
    public function getNextEventNumber()
    {
        return $this->nextEventNumber;
    }

    /**
     * @return mixed
     */
    public function getReadDirection()
    {
        return $this->readDirection;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }
}