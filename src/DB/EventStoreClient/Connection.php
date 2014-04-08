<?php

namespace DB\EventStoreClient;

/**
 * Class Connection
 * @package DB\EventStoreClient
 */
class Connection implements ConnectionInterface
{
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
        // TODO: Implement deleteStream() method.
    }
}
