<?php

namespace EventStore\Tests;

use EventStore\Event;
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
    public function event_is_writed_to_stream()
    {
        $streamName = uniqid();
        $event      = Event::newInstance('Foo', 'bar');

        $this->es->writeToStream($streamName, $event);

        $this->assertEquals('201', $this->es->getLastResponse()->getStatusCode());
    }

    /** @test */
    public function stream_is_soft_deleted()
    {
        $streamName = uniqid();
        $event      = Event::newInstance('Foo', 'bar');

        $this->es->writeToStream($streamName, $event);
        $this->es->deleteStream($streamName, StreamDeletion::SOFT());

        $this->assertEquals('204', $this->es->getLastResponse()->getStatusCode());

        // we try to write to a soft deleted stream...
        $this->es->writeToStream($streamName, $event);

        // ..and we should expect a "201 Created" response
        $this->assertEquals('201', $this->es->getLastResponse()->getStatusCode());
    }

    /** @test */
    public function stream_is_hard_deleted()
    {
        $streamName = uniqid();
        $event      = Event::newInstance('Foo', 'bar');

        $this->es->writeToStream($streamName, $event);
        $this->es->deleteStream($streamName, StreamDeletion::HARD());

        $this->assertEquals('204', $this->es->getLastResponse()->getStatusCode());

        // we try to write to a hard deleted stream...
        $this->es->writeToStream($streamName, $event);

        // ..and we should expect a "410 Stream deleted" response
        $this->assertEquals('410', $this->es->getLastResponse()->getStatusCode());
    }

}
