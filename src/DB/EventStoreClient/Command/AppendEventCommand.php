<?php

namespace DB\EventStoreClient\Command;

/**
 * Class AppendEventCommand
 * @package DB\EventStoreClient\Command
 */
class AppendEventCommand
{
    /**
     * @var string
     */
    private $eventId;

    /**
     * @var string
     */
    private $eventType;

    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $expectedVersion;

    /**
     * @param string $eventId
     * @param string $eventType
     * @param array  $data
     * @param int    $expectedVersion
     */
    public function __construct($eventId, $eventType, array $data, $expectedVersion = -2)
    {
        $this->eventId = $eventId;
        $this->eventType = $eventType;
        $this->data = $data;
        $this->expectedVersion = $expectedVersion;
    }

    /**
     * @return string
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getExpectedVersion()
    {
        return $this->expectedVersion;
    }
}
