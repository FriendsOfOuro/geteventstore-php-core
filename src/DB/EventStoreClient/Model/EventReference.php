<?php

namespace DB\EventStoreClient\Model;

/**
 * Class EventReference
 * @package DB\EventStoreClient\Model
 */
final class EventReference
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
    private function __construct(StreamReference $streamReference, $streamVersion)
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

    public static function fromNameAndVersion(StreamReference $streamReference, $streamVersion)
    {
        return new self($streamReference, $streamVersion);
    }
}
