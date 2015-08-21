<?php

namespace EventStore\ValueObjects\Tests\Util;

use EventStore\ValueObjects\Tests\TestCase;
use EventStore\ValueObjects\Util\Util;

class UtilTest extends TestCase
{
    public function testClassEquals()
    {
        $util1 = new Util();
        $util2 = new Util();

        $this->assertTrue(Util::classEquals($util1, $util2));
        $this->assertFalse(Util::classEquals($util1, $this));
    }

    public function testGetClassAsString()
    {
        $util = new Util();
        $this->assertEquals(Util::class, Util::getClassAsString($util));
    }
}
