<?php
namespace EventStore\ValueObjects\Tests\Enum;

use EventStore\ValueObjects\Enum\Enum;
use EventStore\ValueObjects\Tests\TestCase;

class EnumTest extends TestCase
{
    public function testSameValueAs()
    {
        $stub1 = $this->getMock('EventStore\ValueObjects\Enum\Enum', array(), array(), '', false);
        $stub2 = $this->getMock('EventStore\ValueObjects\Enum\Enum', array(), array(), '', false);

        $stub1->expects($this->any())
              ->method('sameValueAs')
              ->will($this->returnValue(true));

        $this->assertTrue($stub1->sameValueAs($stub2));
    }

    public function testToString()
    {
        $stub = $this->getMock('EventStore\ValueObjects\Enum\Enum', array(), array(), '', false);

        $this->assertEquals('', $stub->__toString());
    }
}
