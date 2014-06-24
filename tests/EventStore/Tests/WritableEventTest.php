<?php

namespace EventStore\Tests;

use EventStore\WritableEvent;
use ValueObjects\Identity\UUID;

class WritableEventTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function event_is_converted_to_stream_data()
    {
        $uuid  = new UUID();
        $event = new WritableEvent($uuid, 'Foo', 'bar');
        $streamData = [
            'eventId'   => $uuid->toNative(),
            'eventType' => 'Foo',
            'data'      => 'bar'
        ];

        $this->assertEquals($streamData, $event->toStreamData());
    }

}
