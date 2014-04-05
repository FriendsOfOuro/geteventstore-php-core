<?php

namespace DB\EventStoreClient;

/**
 * Class Event
 * @package DB\EventStoreClient
 */
class Event
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
     * @param $eventId
     * @param $eventType
     * @param array $data
     */
    public function __construct($eventId, $eventType, array $data)
    {
        $this->eventId = $eventId;
        $this->eventType = $eventType;
        $this->data = $data;
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
}
