<?php

namespace DB\EventStoreClient\Tests;

use DB\EventStoreClient\Connection;
use DB\EventStoreClient\Tests\Guzzle\GuzzleTestCase;
use GuzzleHttp\Message\Response;

class ConnectionTest extends GuzzleTestCase
{
    public function testSoftDeleteStreamWorksProperly()
    {
        $guzzle = $this->buildMockClient(function () {
            return new Response(204);
        });

        $connection = new Connection($guzzle);
        $connection->deleteStream('example');

        $this->assertInstanceOf('GuzzleHttp\\Message\\RequestInterface', $this->request);
        $this->assertEquals('DELETE', $this->request->getMethod());
        $this->assertEquals('/streams/example', $this->request->getResource());
        $this->assertEquals('false', $this->request->getHeader('ES-HardDelete'));
    }
}
