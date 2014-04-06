<?php

namespace DB\EventStoreClient\Model;

/**
 * Class StreamReference
 * @package DB\EventStoreClient\Model
 */
final class StreamReference
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
    private function __construct($streamName)
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getStreamName();
    }

    /**
     * @param $streamName
     * @return StreamReference
     */
    public static function fromName($streamName)
    {
        return new self($streamName);
    }
}
