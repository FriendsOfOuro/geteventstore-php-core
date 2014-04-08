<?php

namespace DB\EventStoreClient\Tests;

use DB\EventStoreClient\Connection;
use DB\EventStoreClient\Tests\Guzzle\GuzzleTestCase;
use GuzzleHttp\Message\Response;

class ConnectionTest extends GuzzleTestCase
{
    /**
     * @dataProvider deleteDataProvider
     * @param $softDelete
     * @param $expectedHeader
     */
    public function testSoftDeleteStreamWorksProperly($softDelete, $expectedHeader)
    {
        $guzzle = $this->buildMockClient(function () {
            return new Response(204);
        });

        $connection = new Connection($guzzle);
        $connection->deleteStream('example', $softDelete);

        $this->assertInstanceOf('GuzzleHttp\\Message\\RequestInterface', $this->request);
        $this->assertEquals('DELETE', $this->request->getMethod());
        $this->assertEquals('/streams/example', $this->request->getResource());
        $this->assertEquals($expectedHeader, $this->request->getHeader('ES-HardDelete'));
    }

    public static function deleteDataProvider()
    {
        return [
            [false, 'false'],
            [true, 'true']
        ];
    }
}
