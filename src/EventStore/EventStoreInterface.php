<?php
namespace EventStore;

use EventStore\StreamFeed\StreamFeed;
use EventStore\StreamFeed\EntryEmbedMode;
use GuzzleHttp\Message\ResponseInterface;
use EventStore\StreamFeed\Event;
use EventStore\StreamFeed\LinkRelation;

/**
 * Interface EventStoreInterface
 * @package EventStore
 */
interface EventStoreInterface
{
    /**
     * Navigate stream feed through link relations
     *
     * @param  StreamFeed      $stream_feed The stream feed to navigate through
     * @param  LinkRelation    $relation    The "direction" expressed as link relation
     * @return null|StreamFeed
     */
    public function navigateStreamFeed(StreamFeed $stream_feed, LinkRelation $relation);

    /**
     * Get the response from the last HTTP call to the EventStore API
     *
     * @return ResponseInterface
     */
    public function getLastResponse();

    /**
     * Write one or more events to a stream
     *
     * @param  string                                  $stream_name     The stream name
     * @param  WritableToStream                        $events          Single event or a collection of events
     * @param  int                                     $expectedVersion The expected version of the stream
     * @throws Exception\WrongExpectedVersionException
     */
    public function writeToStream($stream_name, WritableToStream $events, $expectedVersion = ExpectedVersion::ANY);

    /**
     * Read a single event
     *
     * @param  string $event_url The url of the event
     * @return Event
     */
    public function readEvent($event_url);

    /**
     * Delete a stream
     *
     * @param string         $stream_name Name of the stream
     * @param StreamDeletion $mode        Deletion mode (soft or hard)
     */
    public function deleteStream($stream_name, StreamDeletion $mode);

    /**
     * Open a stream feed for read and navigation
     *
     * @param  string         $stream_name The stream name
     * @param  EntryEmbedMode $embed_mode  The event entries embed mode (none, rich or body)
     * @return StreamFeed
     */
    public function openStreamFeed($stream_name, EntryEmbedMode $embed_mode = null);
}
