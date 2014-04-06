<?php

namespace DB\EventStoreClient\Adapter;

use DB\EventStoreClient\Command\AppendEventCommand;
use GuzzleHttp\ClientInterface;

class HttpStreamAdapter
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $streamName;

    public function __construct(ClientInterface $client, $streamName)
    {
        $this->client = $client;
        $this->streamName = $streamName;
    }

    public function applyAppend(AppendEventCommand $command)
    {
        $this
            ->client
            ->post('/streams/'.$this->streamName)
        ;
    }
}
