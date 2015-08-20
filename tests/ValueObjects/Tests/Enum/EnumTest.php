<?php

namespace ValueObjects\Tests\Enum;

use ValueObjects\Tests\TestCase;
use ValueObjects\Enum\Enum;

class EnumTest extends TestCase
{
    public function testSameValueAs()
    {
        $stub1 = $this->getMock('ValueObjects\Enum\Enum', array(), array(), '', false);
        $stub2 = $this->getMock('ValueObjects\Enum\Enum', array(), array(), '', false);

        $stub1->expects($this->any())
              ->method('sameValueAs')
              ->will($this->returnValue(true));

        $this->assertTrue($stub1->sameValueAs($stub2));
    }

    public function testToString()
    {
        $stub = $this->getMock('ValueObjects\Enum\Enum', array(), array(), '', false);

        $this->assertEquals('', $stub->__toString());
    }
}
