<?php

namespace DB\EventStoreClient\Model;

/**
 * Class EventReference
 * @package DB\EventStoreClient\Model
 */
class EventReference
{
    /**
     * @var StreamReference
     */
    private $streamReference;

    /**
     * @var int
     */
    private $streamVersion;

    /**
     * @param StreamReference $streamReference
     * @param int             $streamVersion
     */
    public function __construct(StreamReference $streamReference, $streamVersion)
    {
        $this->streamReference = $streamReference;
        $this->streamVersion = $streamVersion;
    }

    /**
     * @return StreamReference
     */
    public function getStreamReference()
    {
        return $this->streamReference;
    }

    /**
     * @return int
     */
    public function getStreamVersion()
    {
        return $this->streamVersion;
    }
}
