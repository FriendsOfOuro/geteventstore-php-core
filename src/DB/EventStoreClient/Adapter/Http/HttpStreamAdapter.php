<?php

namespace DB\EventStoreClient\Adapter\Http;

use DB\EventStoreClient\Adapter\StreamAdapterInterface;
use DB\EventStoreClient\Command\AppendEventCommand;
use DB\EventStoreClient\Model\EventReference;
use GuzzleHttp\ClientInterface;

/**
 * Class HttpStreamAdapter
 * @package DB\EventStoreClient\Adapter
 */
class HttpStreamAdapter implements StreamAdapterInterface
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

    /**
     * @param  AppendEventCommand  $command
     * @return EventReference|null
     */
    public function applyAppend(AppendEventCommand $command)
    {
        $response = $this
            ->client
            ->post('/streams/'.$this->streamName,[
                'headers' => [
                    'Content-type' => 'application/json'
                ],
                'body' => json_encode([$this->commandToArray($command)])
            ])
        ;

        $locationExploded = explode('/', $response->getHeader('Location'));

        if (count($locationExploded) < 6) {
            return null;
        }

        $streamName = $locationExploded[4];
        $streamVersion = (int) $locationExploded[5];

        return new EventReference($streamName, $streamVersion);
    }

    /**
     * @param  AppendEventCommand $command
     * @return array
     */
    private function commandToArray(AppendEventCommand $command)
    {
        return [
            'eventId' => $command->getEventId(),
            'eventType' => $command->getEventType(),
            'data' => $command->getData(),
        ];
    }
}
