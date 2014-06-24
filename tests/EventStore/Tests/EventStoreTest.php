<?php

namespace EventStore\Tests;

use EventStore\WritableEvent;
use EventStore\EventStore;
use EventStore\StreamDeletion;

class EventStoreTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->es = new EventStore('http://127.0.0.1:2113');
    }

    /** @test */
    public function client_successfully_connects_to_event_store()
    {
        $this->assertEquals('200', $this->es->getLastResponse()->getStatusCode());
    }

    /** @test */
    public function event_is_written_to_stream()
    {
        $this->prepareTestStream();

        $this->assertEquals('201', $this->es->getLastResponse()->getStatusCode());
    }

    /** @test */
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

    /** @test */
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

    /** @test */
    public function stream_is_successfully_read()
    {
        $streamName = $this->prepareTestStream();
        $stream = $this->es->readStream($streamName);

        $this->assertEquals($streamName, $stream->getName());
    }

    private function prepareTestStream()
    {
        $streamName = uniqid();
        $event      = WritableEvent::newInstance('Foo', 'bar');

        $this->es->writeToStream($streamName, $event);

        return $streamName;
    }

}
