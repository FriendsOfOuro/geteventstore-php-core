<?php

namespace DB\EventStoreClient\Adapter\Http;

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
}
