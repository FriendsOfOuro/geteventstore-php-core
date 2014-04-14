<?php

namespace EventStore\Tests;

use EventStore\StreamEventsSlice;

class StreamEventsSliceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $slice = new StreamEventsSlice('Success', 0, 'forward', [], 1, 100);

        $this->assertEquals('Success', $slice->getStatus());
        $this->assertSame(0, $slice->getFromEventNumber());
        $this->assertEquals('forward', $slice->getReadDirection());
        $this->assertEquals([], $slice->getEvents());
        $this->assertSame(1, $slice->getNextEventNumber());
        $this->assertSame(100, $slice->getLastEventNumber());
    }
}
