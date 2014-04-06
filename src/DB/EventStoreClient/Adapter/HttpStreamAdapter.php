<?php

namespace DB\EventStoreClient\Adapter;

use DB\EventStoreClient\Command\AppendEventCommand;
use GuzzleHttp\ClientInterface;

/**
 * Class HttpStreamAdapter
 * @package DB\EventStoreClient\Adapter
 */
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

    /**
     * @param ClientInterface $client
     * @param string          $streamName
     */
    public function __construct(ClientInterface $client, $streamName)
    {
        $this->client = $client;
        $this->streamName = $streamName;
    }

    public function applyAppend(AppendEventCommand $command)
    {
        $response = $this
            ->client
            ->post('/streams/'.$this->streamName,[
                'headers' => [
                    'Content-type' => 'application/json'
                ],
                'body' => json_encode([[
                    'eventId'   => $command->getEventId(),
                    'eventType' => $command->getEventType(),
                    'data'      => $command->getData(),
                ]])
            ])
        ;
    }
}
