<?php

namespace EventStore\Tests;

use EventStore\Event;
use EventStore\EventCollection;
use ValueObjects\Identity\UUID;

class EventCollectionTest extends \PHPUnit_Framework_TestCase {

    public function test_event_collection_is_converted_to_stream_data() {
        $uuid1  = new UUID();
        $event1 = new Event($uuid1, 'Foo', 'bar');

        $uuid2  = new UUID();
        $event2 = new Event($uuid2, 'Baz', 'foo');

        $eventCollection = new EventCollection([$event1, $event2]);

        $streamData = [
            [
                'eventId'   => $uuid1->toNative(),
                'eventType' => 'Foo',
                'data'      => 'bar'
            ], [
                'eventId'   => $uuid2->toNative(),
                'eventType' => 'Baz',
                'data'      => 'foo'
            ]
        ];

        $this->assertEquals($streamData, $eventCollection->toStreamData());
    }

}