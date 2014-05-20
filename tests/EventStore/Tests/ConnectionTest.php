<?php

namespace EventStore\Tests;

use EventStore\Connection;
use EventStore\ConnectionInterface;
use EventStore\EventData;
use EventStore\Tests\Guzzle\GuzzleTestCase;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class ConnectionTest extends GuzzleTestCase
{
    public function testHardDeleteStreamWorksProperly()
    {
        $this->streamDeleteCommon(true);
        $this->assertEquals('true', $this->request->getHeader('ES-HardDelete'));
    }

    public function testSoftDeleteStreamHasNotHardDeleteHeader()
    {
        $this->streamDeleteCommon(false);
        $this->assertFalse($this->request->hasHeader('ES-HardDelete'), 'ES-HardDelete header should not be present');
    }

    private function streamDeleteCommon($hardDelete)
    {
        $guzzle = $this->buildMockClient(function () {
            return new Response(204);
        });

        $connection = Connection::create(['client' => $guzzle]);
        $connection->deleteStream('example', $hardDelete);

        $this->assertRequestPresent();
        $this->assertEquals('DELETE', $this->request->getMethod());
        $this->assertEquals('/streams/example', $this->request->getResource());

        $this->assertEquals('application/json', $this->request->getHeader('Content-type'));
    }

    public function testAppendEventMakesCorrectHttpRequest()
    {
        $guzzle = $this->buildMockClient(function () {
            return new Response(201);
        });

        $connection = Connection::create(['client' => $guzzle]);

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
        $this->assertEquals('application/json', $this->request->getHeader('Content-type'));
    }

    public function testReadStreamEventsForward()
    {
        $direction = 'forward';
        $start = 0;
        $count = 2;

        $this->readStreamCommonAssertions($direction, $start, $count);
    }

    /**
     * @param $jsonFile
     * @return Response
     */
    private function createJsonFeedResponse($jsonFile)
    {
        $fh = fopen($jsonFile, 'r');

        $response = new Response(200);
        $body = Stream::factory($fh);
        $response->setBody($body);
        $response->addHeader('Content-type', 'application/vnd.eventstore.atom+json; charset=utf-8');

        return $response;
    }

    /**
     * @param $jsonFile
     * @return Connection
     */
    private function mockConnectionToJson($jsonFile)
    {
        $guzzle = $this->buildMockClient(function () use ($jsonFile) {
            return $this->createJsonFeedResponse($jsonFile);
        });
        $connection = Connection::create(['client' => $guzzle]);

        return $connection;
    }

    /**
     * @param string $direction
     * @param int    $start
     * @param int    $count
     */
    private function readStreamCommonAssertions($direction, $start, $count)
    {
        $jsonFile = sprintf('%s/%s.json', __DIR__, $direction);
        $connection = $this->mockConnectionToJson($jsonFile);

        $slice = $connection->readStreamEventsForward('test', $start, $count, false);

        $this->assertNotNull($this->request);

        $resource = sprintf('/streams/test/%d/%s/%d?embed=body', $start, $direction, $count);
        $this->assertEquals($resource, $this->request->getResource());
        $this->assertEquals('application/vnd.eventstore.atom+json', $this->request->getHeader('accept'));

        $this->assertInstanceOf('EventStore\StreamEventsSlice', $slice);
        $nextEvent = $start + ($direction === 'forward' ? $count : -$count);

        $this->assertSame($nextEvent, $slice->getNextEventNumber());
    }
}
