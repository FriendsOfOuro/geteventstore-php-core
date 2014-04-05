<?php

namespace DB\EventStoreClient\Tests\Command;

use DB\EventStoreClient\Command\AppendEventCommandFactory;

class AppendEventCommandFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AppendEventCommandFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new AppendEventCommandFactory();
    }

    public function testCreateWorksProperly()
    {
        $eventType = 'event-type';
        $data = ['foo' => 'bar'];

        $command = $this->factory->create($eventType, $data);

        $this->assertRegExp('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $command->getEventId());
        $this->assertEquals($eventType, $command->getEventType());
        $this->assertEquals($data, $command->getData());
    }
}
