<?php

namespace EventStore\Tests;

use EventStore\EventEmbedMode;
use EventStore\StreamFeedLinkRelation;
use EventStore\WritableEvent;
use EventStore\EventStore;
use EventStore\StreamDeletion;
use EventStore\WritableEventCollection;

class EventStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventStore
     */
    private $es;

    public function setup()
    {
        $this->es = new EventStore('http://127.0.0.1:2113');
    }

    /** @test */
    public function client_successfully_connects_to_event_store()
    {
        $this->assertEquals('200', $this->es->getLastResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function event_is_written_to_stream()
    {
        $this->prepareTestStream();

        $this->assertEquals('201', $this->es->getLastResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function stream_is_soft_deleted()
    {
        $streamName = $this->prepareTestStream();
        $this->es->deleteStream($streamName, StreamDeletion::SOFT());

        $this->assertEquals('204', $this->es->getLastResponse()->getStatusCode());

        // we try to write to a soft deleted stream...
        $this->es->writeToStream($streamName, WritableEvent::newInstance('Foo', 'bar'));

        // ..and we should expect a "201 Created" response
        $this->assertEquals('201', $this->es->getLastResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function stream_is_hard_deleted()
    {
        $streamName = $this->prepareTestStream();
        $this->es->deleteStream($streamName, StreamDeletion::HARD());

        $this->assertEquals('204', $this->es->getLastResponse()->getStatusCode());

        // we try to write to a hard deleted stream...
        $this->es->writeToStream($streamName, WritableEvent::newInstance('Foo', 'bar'));

        // ..and we should expect a "410 Stream deleted" response
        $this->assertEquals('410', $this->es->getLastResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function stream_feed_is_successfully_opened()
    {
        $streamName = $this->prepareTestStream();
        $streamFeed = $this->es->openStreamFeed($streamName);

        $json = $streamFeed->getJson();

        $this->assertEquals($streamName, $json['streamId']);
    }

    /**
     * @test
     * @expectedException \EventStore\Exception\ConnectionFailedException
     */
    public function unreacheable_event_store_throws_exception()
    {
        new EventStore('http://127.0.0.1:12345/');
    }

    /** @test */
    public function event_data_is_embedded_with_body_mode()
    {
        $streamName = $this->prepareTestStream();
        $streamFeed = $this->es->openStreamFeed($streamName, EventEmbedMode::BODY());

        $json = $streamFeed->getJson();

        $this->assertEquals(['foo' => 'bar'], json_decode($json['entries'][0]['data'], true));
    }

    /** @test */
    public function event_stream_feed_head_returns_next_link()
    {
        $streamName = $this->prepareTestStream(40);

        $head = $this->es->openStreamFeed($streamName);
        $next = $this->es->navigateStreamFeed($head, StreamFeedLinkRelation::NEXT());

        $this->assertInstanceOf('EventStore\StreamFeed', $next);
        $this->assertCount(20, $next->getJson()['entries']);
    }

    /**
     * @param  int    $length
     * @return string
     */
    private function prepareTestStream($length = 1)
    {
        $streamName = uniqid();
        $events     = [];

        for ($i = 0; $i < $length; ++$i) {
            $events[] = WritableEvent::newInstance('Foo', ['foo' => 'bar']);
        }

        $collection = new WritableEventCollection($events);
        $this->es->writeToStream($streamName, $collection);

        return $streamName;
    }
}
