<?php

namespace EventStore\Tests;

use EventStore\WritableEvent;
use EventStore\WritableEventCollection;
use ValueObjects\Identity\UUID;

class WritableEventCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function event_collection_is_converted_to_stream_data()
    {
        $uuid1  = new UUID();
        $event1 = new WritableEvent($uuid1, 'Foo', 'bar');

        $uuid2  = new UUID();
        $event2 = new WritableEvent($uuid2, 'Baz', 'foo');

        $eventCollection = new WritableEventCollection([$event1, $event2]);

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

    /**
     * @test
     * @expectedException EventStore\Exception\InvalidWritableEventObjectException
     */
    public function invalid_collection_throws_exception()
    {
        new WritableEventCollection(['foobar']);
    }
}
