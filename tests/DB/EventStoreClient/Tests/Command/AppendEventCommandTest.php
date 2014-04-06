<?php

namespace DB\EventStoreClient\Tests\Command;

use DB\EventStoreClient\Command\AppendEventCommand;

class AppendEventCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testEventIdGetterReturnsProperValue()
    {
        $uuid = 'd776ad80-1471-4b42-a1e7-ae2960b84abc';

        $event = new AppendEventCommand($uuid, 'event-type', []);

        $this->assertEquals($uuid, $event->getEventId());
    }

    public function testEventTypeGetterReturnsProperValue()
    {
        $eventType = 'event-type';

        $event = new AppendEventCommand('d776ad80-1471-4b42-a1e7-ae2960b84abc', $eventType, []);

        $this->assertEquals($eventType, $event->getEventType());
    }

    public function testDataGetterReturnsProperValue()
    {
        $data = ['foo' => 'bar'];
        $event = new AppendEventCommand('d776ad80-1471-4b42-a1e7-ae2960b84abc', 'event-type', $data);
        $this->assertEquals($data, $event->getData());
    }

    public function testExpectedVersionGetterReturnsDefaultValue()
    {
        $event = new AppendEventCommand('d776ad80-1471-4b42-a1e7-ae2960b84abc', 'event-type', ['foo' => 'bar']);
        $this->assertEquals(-2, $event->getExpectedVersion());
    }

    public function testExpectedVersionGetterReturnsSetValue()
    {
        $expectedVersion = 10;

        $event = new AppendEventCommand(
            'd776ad80-1471-4b42-a1e7-ae2960b84abc',
            'event-type',
            ['foo' => 'bar'],
            $expectedVersion
        );

        $this->assertSame($expectedVersion, $event->getExpectedVersion());
    }
}
