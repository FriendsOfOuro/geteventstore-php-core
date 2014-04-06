<?php

namespace DB\EventStoreClient\Adapter\Http;

use DB\EventStoreClient\Model\EventReference;
use DB\EventStoreClient\Model\StreamReference;
use GuzzleHttp\ClientInterface;

abstract class HttpEventStreamAdapter
{
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var string
     */
    private $streamReference;

    /**
     * @param ClientInterface $client
     * @param StreamReference $streamReference
     */
    public function __construct(ClientInterface $client, StreamReference $streamReference)
    {
        $this->client = $client;
        $this->streamReference = $streamReference;
    }

    /**
     * @return ClientInterface
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    protected function getStreamReference()
    {
        return $this->streamReference;
    }

    /**
     * @return string
     */
    protected function getStreamUri()
    {
        return '/streams/' . $this->getStreamReference()->getStreamName();
    }

    /**
     * @param $location
     * @return EventReference|null
     */
    protected function locationToEventReference($location)
    {
        $locationExploded = explode('/', $location);

        $count = count($locationExploded);
        if ($count < 6) {
            return null;
        }

        $streamReference = $locationExploded[$count - 2];
        $streamVersion = (int) $locationExploded[$count - 1];

        return EventReference::fromStreamReferenceAndVersion(StreamReference::fromName($streamReference), $streamVersion);
    }
}
