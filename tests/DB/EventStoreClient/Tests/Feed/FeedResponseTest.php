<?php

namespace DB\EventStoreClient\Tests\Feed;

use DB\EventStoreClient\Feed\FeedResponse;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class FeedResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testStatusCodeIsReturnedProperly()
    {
        $statusCode = 200;
        $response = FeedResponse::fromGuzzleResponse(new Response($statusCode));

        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    public function testBodyIsReturnedProperly()
    {
        $body = Stream::factory('Hello World!');
        $response = FeedResponse::fromGuzzleResponse(new Response(200, [], $body));

        $this->assertInternalType('string', $response->getBody());

        $this->assertEquals((string) $body, $response->getBody());
    }
}
