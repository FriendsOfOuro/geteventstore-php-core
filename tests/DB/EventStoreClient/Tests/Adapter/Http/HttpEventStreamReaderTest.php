<?php

namespace DB\EventStoreClient\Tests\Adapter\Http;

use DB\EventStoreClient\Tests\Guzzle\GuzzleTestCase;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class HttpEventStreamReaderTest extends GuzzleTestCase
{
    public function testExploration1()
    {
        $client = $this->buildMockClient(function () {
            $body = Stream::factory(fopen(__DIR__.'/feed.xml', 'r'));

            return new Response(200, [], $body);
        });

        $this->markTestIncomplete();
    }
}
