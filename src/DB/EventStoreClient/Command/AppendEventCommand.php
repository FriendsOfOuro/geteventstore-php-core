<?php

namespace DB\EventStoreClient\Command;

/**
 * Class AppendEventCommand
 * @package DB\EventStoreClient\Command
 */
class AppendEventCommand
{
    /**
     * Disables optimistic locking
     */
    const VERSION_ANY = -2;

    /**
     * Expecting that streams does not exists (will be automatically created)
     */
    const VERSION_NOT_EXISTING = -1;

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
    public function __construct($eventId, $eventType, array $data, $expectedVersion = self::VERSION_ANY)
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
