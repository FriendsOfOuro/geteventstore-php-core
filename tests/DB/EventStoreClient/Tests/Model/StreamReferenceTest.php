<?php

namespace DB\EventStoreClient\Tests\Model;

use DB\EventStoreClient\Model\StreamReference;

class StreamReferenceTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersReturnProperValue()
    {
        $streamName = 'streamname';

        $reference = new StreamReference($streamName);

        $this->assertSame($streamName, $reference->getStreamName());
    }
}
