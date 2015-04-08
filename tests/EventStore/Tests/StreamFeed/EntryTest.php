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

    /**
     * @test
     */
    public function theEventTypeForEntriesOfAnProjectionStreamFeedIsCorrect()
    {
        $json = file_get_contents(__DIR__ . '/Fixtures/RichEventStoreCeEntryResponse.json');
        $entryData = json_decode($json, TRUE);

        $entry = new Entry($entryData);

        $this->assertEquals('ThingsHappenedEvent', $entry->getType());
    }
}
