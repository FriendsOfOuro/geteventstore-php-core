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

    /**
     * @param  StreamReference $streamReference
     * @param  int             $streamVersion
     * @return EventReference
     */
    public static function fromStreamReferenceAndVersion(StreamReference $streamReference, $streamVersion)
    {
        return new self($streamReference, $streamVersion);
    }

    /**
     * @param  string         $streamName
     * @param  string         $streamVersion
     * @return EventReference
     */
    public static function fromStreamNameAndVersion($streamName, $streamVersion)
    {
        return new self(StreamReference::fromName($streamName), $streamVersion);
    }
}
