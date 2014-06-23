<?php

namespace EventStore\Tests;

use EventStore\Event;
use ValueObjects\Identity\UUID;

class EventTest extends \PHPUnit_Framework_TestCase {

    /** @test */
    public function event_is_converted_to_stream_data() {
        $uuid  = new UUID();
        $event = new Event($uuid, 'Foo', 'bar');
        $streamData = [
            'eventId'   => $uuid->toNative(),
            'eventType' => 'Foo',
            'data'      => 'bar'
        ];

        $this->assertEquals($streamData, $event->toStreamData());
    }

}