<?php

namespace DB\EventStoreClient;
use GuzzleHttp\ClientInterface;

/**
 * Class Connection
 * @package DB\EventStoreClient
 */
class Connection implements ConnectionInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * Constructor
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function appendToStream($stream, $expectedVersion, array $events)
    {
        // TODO: Implement appendToStream() method.
    }

    /**
     * {@inheritdoc}
     */
    public function readStreamEventsForward($stream, $start, $count, $resolveLinkTos)
    {
        // TODO: Implement readStreamEventsForward() method.
    }

    /**
     * {@inheritdoc}
     */
    public function readStreamEventsBackward($stream, $start, $count, $resolveLinkTos)
    {
        // TODO: Implement readStreamEventsBackward() method.
    }

    /**
     * {@inheritdoc}
     */
    public function deleteStream($stream, $hardDelete = false)
    {
        $this
            ->client
            ->delete('/streams/'.$stream, [
                'headers' => [
                    'ES-HardDelete' => $hardDelete ? 'true' : 'false'
                ]
            ])
        ;
    }
}
