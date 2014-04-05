<?php

namespace DB\EventStoreClient\Tests;

use DB\EventStoreClient\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testEventIdGetterReturnsProperValue()
    {
        $uuid = 'd776ad80-1471-4b42-a1e7-ae2960b84abc';

        $event = new Event($uuid, 'event-type', []);

        $this->assertEquals($uuid, $event->getEventId());
    }

    public function testEventTypeGetterReturnsProperValue()
    {
        $eventType = 'event-type';

        $event = new Event('d776ad80-1471-4b42-a1e7-ae2960b84abc', $eventType, []);

        $this->assertEquals($eventType, $event->getEventType());
    }

    public function testDataGetterReturnsProperValue()
    {
        $data = ['foo' => 'bar'];
        $event = new Event('d776ad80-1471-4b42-a1e7-ae2960b84abc', 'event-type', $data);
        $this->assertEquals($data, $event->getData());
    }
}
