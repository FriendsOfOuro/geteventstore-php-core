<?php

namespace DB\EventStoreClient\Tests\Model;

use DB\EventStoreClient\Model\EventReference;

class EventReferenceTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersReturnProperValue()
    {
        $streamName = 'streamname';
        $streamVersion = 10;

        $reference = new EventReference($streamName, $streamVersion);

        $this->assertSame($streamName, $reference->getStreamName());
        $this->assertSame($streamVersion, $reference->getStreamVersion());
    }
}
