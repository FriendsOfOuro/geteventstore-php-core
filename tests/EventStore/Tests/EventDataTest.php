<?php

namespace EventStore\Tests;

use EventStore\EventData;

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

    public function testToArray()
    {
        $array = [
            'eventId'   => 'd776ad80-1471-4b42-a1e7-ae2960b84abc',
            'eventType' => 'event-type',
            'data'      => ['foo' => 'bar']
        ];
        $event = new EventData('d776ad80-1471-4b42-a1e7-ae2960b84abc', 'event-type', ['foo' => 'bar']);

        $this->assertEquals($array, $event->toArray());
    }
}
