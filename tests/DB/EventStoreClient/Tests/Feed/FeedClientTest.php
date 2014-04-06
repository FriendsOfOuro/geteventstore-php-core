<?php

namespace DB\EventStoreClient\Tests\Feed;

use DB\EventStoreClient\Feed\FeedClient;
use DB\EventStoreClient\Tests\Guzzle\GuzzleTestCase;
use GuzzleHttp\Adapter\TransactionInterface;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class FeedClientTest extends GuzzleTestCase
{
    public function testRequestIsTranslatedProperly()
    {
        $statusCode = 200;
        $body = 'Hello World!';

        $client = $this->buildMockClient(function (TransactionInterface $trans) use ($statusCode, $body) {
            return new Response($statusCode, [], Stream::factory($body));
        });

        $feedClient = new FeedClient($client);

        $response = $feedClient->get('/dummy');

        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($body, $response->getBody());
    }
}
