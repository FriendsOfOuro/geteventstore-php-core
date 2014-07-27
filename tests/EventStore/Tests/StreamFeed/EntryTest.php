<?php
namespace EventStore\Tests\StreamFeed;

use EventStore\StreamFeed\Entry;

class EntryTest extends \PHPUnit_Framework_TestCase
{
    public function testTypeGetter()
    {
        $entry = new Entry(['summary' => 'Bar']);

        $this->assertEquals('Bar', $entry->getType());
    }
}
