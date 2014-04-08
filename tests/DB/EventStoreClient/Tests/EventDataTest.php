<?php

namespace DB\EventStoreClient\Tests;

use DB\EventStoreClient\EventData;

class EventDataTest extends \PHPUnit_Framework_TestCase
{
    public function testEventIdGetterReturnsProperValue()
    {
        $uuid = 'd776ad80-1471-4b42-a1e7-ae2960b84abc';

        $event = new EventData($uuid, 'event-type', []);

        $this->assertEquals($uuid, $event->getEventId());
    }

    public function testTypeGetterReturnsProperValue()
    {
        $eventType = 'event-type';

        $event = new EventData('d776ad80-1471-4b42-a1e7-ae2960b84abc', $eventType, []);

        $this->assertEquals($eventType, $event->getType());
    }

    public function testDataGetterReturnsProperValue()
    {
        $data = ['foo' => 'bar'];
        $event = new EventData('d776ad80-1471-4b42-a1e7-ae2960b84abc', 'event-type', $data);
        $this->assertEquals($data, $event->getData());
    }
}
