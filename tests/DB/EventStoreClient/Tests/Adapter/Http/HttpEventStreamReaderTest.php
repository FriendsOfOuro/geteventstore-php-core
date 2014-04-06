<?php

namespace DB\EventStoreClient\Tests\Adapter\Http;

use DB\EventStoreClient\Adapter\Http\HttpEventStreamReader;
use DB\EventStoreClient\Model\StreamReference;
use DB\EventStoreClient\Tests\Guzzle\GuzzleTestCase;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class HttpEventStreamReaderTest extends GuzzleTestCase
{
    public function testReferenceToLatestEventIsReturnedCorrectly()
    {
        $client = $this->buildMockClient(function () {
            $body = Stream::factory(fopen(__DIR__.'/feed.xml', 'r'));

            return new Response(200, [], $body);
        });

        $streamReference = StreamReference::fromName('example');
        $reader = new HttpEventStreamReader($client, $streamReference);

        $reader->load();

        $this->assertEquals('application/atom+xml', $this->request->getHeader('Accept'));

        $eventReference = $reader->getCurrent();

        $this->assertInstanceOf('DB\\EventStoreClient\\Model\\EventReference', $eventReference);

        $this->assertEquals($streamReference, $eventReference->getStreamReference());
        $this->assertEquals(1, $eventReference->getStreamVersion());
    }
}
