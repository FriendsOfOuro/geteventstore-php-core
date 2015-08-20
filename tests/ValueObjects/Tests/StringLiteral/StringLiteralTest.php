<?php

namespace ValueObjects\Tests\StringLiteral;

use ValueObjects\Tests\TestCase;
use ValueObjects\StringLiteral\StringLiteral;

class StringLiteralTest extends TestCase
{
    public function testFromNative()
    {
        $string = StringLiteral::fromNative('foo');
        $constructedString = new StringLiteral('foo');

        $this->assertTrue($string->sameValueAs($constructedString));
    }

    public function testToNative()
    {
        $string = new StringLiteral('foo');
        $this->assertEquals('foo', $string->toNative());
    }

    public function testSameValueAs()
    {
        $foo1 = new StringLiteral('foo');
        $foo2 = new StringLiteral('foo');
        $bar = new StringLiteral('bar');

        $this->assertTrue($foo1->sameValueAs($foo2));
        $this->assertTrue($foo2->sameValueAs($foo1));
        $this->assertFalse($foo1->sameValueAs($bar));

        $mock = $this->getMock('ValueObjects\ValueObjectInterface');
        $this->assertFalse($foo1->sameValueAs($mock));
    }

    /** @expectedException \ValueObjects\Exception\InvalidNativeArgumentException */
    public function testInvalidNativeArgument()
    {
        new StringLiteral(12);
    }

    public function testIsEmpty()
    {
        $string = new StringLiteral('');

        $this->assertTrue($string->isEmpty());
    }

    public function testToString()
    {
        $foo = new StringLiteral('foo');
        $this->assertEquals('foo', $foo->__toString());
    }
}
