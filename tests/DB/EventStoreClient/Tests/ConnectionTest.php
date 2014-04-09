<?php

namespace DB\EventStoreClient\Tests;

use DB\EventStoreClient\Connection;
use DB\EventStoreClient\ConnectionInterface;
use DB\EventStoreClient\EventData;
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

        $this->assertRequestPresent();
        $this->assertEquals('DELETE', $this->request->getMethod());
        $this->assertEquals('/streams/example', $this->request->getResource());
        $this->assertEquals($expectedHeader, $this->request->getHeader('ES-HardDelete'));
    }

    public function testAppendEventMakesCorrectHttpRequest()
    {
        $guzzle = $this->buildMockClient(function () {
            return new Response(201);
        });

        $connection = new Connection($guzzle);

        $eventId = 'df0582d9-b0c5-4898-93d7-f027b71424b6';
        $type = 'TestEvent';
        $data = ['foo' => 'bar'];

        $event = new EventData($eventId, $type, $data);
        $connection->appendToStream('example', ConnectionInterface::STREAM_VERSION_ANY, [$event]);

        $this->assertRequestPresent();

        $expectedBody = json_encode([[
            'eventId' => $event->getEventId(),
            'eventType' => $event->getType(),
            'data' => $data
        ]]);

        $this->assertEquals(ConnectionInterface::STREAM_VERSION_ANY, $this->request->getHeader('ES-ExpectedVersion'));
        $this->assertJsonStringEqualsJsonString($expectedBody, (string) $this->request->getBody());
    }

    public static function deleteDataProvider()
    {
        return [
            [false, 'false'],
            [true, 'true']
        ];
    }
}
