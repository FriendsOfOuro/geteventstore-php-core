<?php

namespace DB\EventStoreClient\Model;

/**
 * Class EventReference
 * @package DB\EventStoreClient\Model
 */
class EventReference
{
    /**
     * @var string
     */
    private $streamName;

    /**
     * @var int
     */
    private $streamVersion;

    /**
     * Constructor
     *
     * @param $streamName
     * @param $streamVersion
     */
    public function __construct($streamName, $streamVersion)
    {
        $this->streamName = $streamName;
        $this->streamVersion = $streamVersion;
    }

    /**
     * @return string
     */
    public function getStreamName()
    {
        return $this->streamName;
    }

    /**
     * @return int
     */
    public function getStreamVersion()
    {
        return $this->streamVersion;
    }
}
