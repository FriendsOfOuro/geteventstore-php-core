<?php

namespace EventStore;

/**
 * Class StreamEventsSlice
 * @package EventStore
 */
class StreamEventsSlice
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $fromEventNumber;

    /**
     * @var string
     */
    private $readDirection;

    /**
     * @var array
     */
    private $events;

    /**
     * @var int
     */
    private $nextEventNumber;

    /**
     * @param string $status
     * @param int    $fromEventNumber
     * @param string $readDirection
     * @param array  $events
     * @param int    $nextEventNumber
     */
    public function __construct($status, $fromEventNumber, $readDirection, array $events, $nextEventNumber)
    {
        $this->status = $status;
        $this->events = $events;
        $this->fromEventNumber = $fromEventNumber;
        $this->nextEventNumber = $nextEventNumber;
        $this->readDirection = $readDirection;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return int
     */
    public function getFromEventNumber()
    {
        return $this->fromEventNumber;
    }

    /**
     * @return int
     */
    public function getNextEventNumber()
    {
        return $this->nextEventNumber;
    }

    /**
     * @return string
     */
    public function getReadDirection()
    {
        return $this->readDirection;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
