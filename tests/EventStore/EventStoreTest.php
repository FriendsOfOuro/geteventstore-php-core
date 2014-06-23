<?php

namespace EventStore\Tests;

use EventStore\Event;
use EventStore\EventStore;

class EventStoreTest extends \PHPUnit_Framework_TestCase {

    /** @test */
    public function client_successfully_connects_to_event_store() {
        $es = new EventStore('http://127.0.0.1:2113');

        $this->assertEquals('200', $es->getLastResponse()->getStatusCode());
    }

    /** @test */
    public function event_is_writed_to_stream() {
        $es         = new EventStore('http://127.0.0.1:2113');
        $streamName = uniqid();
        $event      = Event::newInstance('Foo', 'bar');

        $es->writeToStream($streamName, $event);

        $this->assertEquals('201', $es->getLastResponse()->getStatusCode());
    }

}