<?php

namespace EventStore\Tests;

use EventStore\ReadEvent;

class ReadEventTest extends \PHPUnit_Framework_TestCase
{
    public function testTypeGetterReturnsProperValue()
    {
        $eventType = 'event-type';

        $event = new ReadEvent($eventType, [], 0);

        $this->assertEquals($eventType, $event->getType());
    }

    public function testDataGetterReturnsProperValue()
    {
        $data = ['foo' => 'bar'];
        $event = new ReadEvent('event-type', $data, 0);
        $this->assertEquals($data, $event->getData());
    }

    public function testVersionGetterReturnsProperValue()
    {
        $version = 1;
        $event = new ReadEvent('event-type', [], $version);

        $this->assertEquals($version, $event->getVersion());
    }
}
