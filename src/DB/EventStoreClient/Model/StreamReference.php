<?php

namespace DB\EventStoreClient\Model;

/**
 * Class StreamReference
 * @package DB\EventStoreClient\Model
 */
class StreamReference
{
    /**
     * @var string
     */
    private $streamName;

    /**
     * Constructor
     *
     * @param string $streamName
     */
    public function __construct($streamName)
    {
        $this->streamName = $streamName;
    }

    /**
     * @return string
     */
    public function getStreamName()
    {
        return $this->streamName;
    }
}
