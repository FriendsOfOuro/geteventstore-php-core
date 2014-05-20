<?php

namespace EventStore\Tests;

use EventStore\Connection;
use EventStore\ConnectionInterface;
use EventStore\EventData;
use EventStore\ReadEvent;
use EventStore\StreamEventsSlice;
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
        $this->assertEquals('application/vnd.eventstore.events+json', $this->request->getHeader('Content-type'));
    }

    /**
     * @expectedException EventStore\Exception\ConcurrencyException
     */
    public function testAppendWithWrongExpectedNumberThrowsConcurrencyException()
    {
        $guzzle = $this->buildMockClient(function () {
            $response = new Response(400);
            $response->setBody(Stream::factory('Wrong expected EventNumber'));

            return $response;
        });

        $connection = Connection::create(['client' => $guzzle]);

        $event = new EventData('df0582d9-b0c5-4898-93d7-f027b71424b6', 'TestEvent', ['foo' => 'bar']);
        $connection->appendToStream('example', 10, [$event]);
    }

    /**
     * @expectedException EventStore\Exception\TransportException
     */
    public function testAppendWith500ThrowsTransportException()
    {
        $guzzle = $this->buildMockClient(function () {
            return new Response(500);
        });

        $connection = Connection::create(['client' => $guzzle]);

        $event = new EventData('df0582d9-b0c5-4898-93d7-f027b71424b6', 'TestEvent', ['foo' => 'bar']);
        $connection->appendToStream('example', 10, [$event]);
    }

    public function testReadStreamEventsForward()
    {
        $this->readStreamCommonAssertions('forward', 0, 2, 2);
    }

    public function testReadStreamEventsBackward()
    {
        $this->readStreamCommonAssertions('backward', 1, 2);
    }

    public function testReadForwardIsDecodedProperly()
    {
        $jsonFile = sprintf('%s/%d_%s_%d.json', __DIR__, 0, 'forward', 2);
        $connection = $this->mockConnectionToJson($jsonFile);

        $slice = $connection->readStreamEventsForward('test', 0, 2, false);
        $events = $slice->getEvents();

        $expected = [
            new ReadEvent('SomethingHappened', ['foo' => 'fizz'], 0),
            new ReadEvent('SomethingElseHappened', ['bar' => 'buzz'], 1),
        ];

        $this->assertEquals($expected, $events);
    }

    public function testReadBackwardIsDecodedProperly()
    {
        $jsonFile = sprintf('%s/%d_%s_%d.json', __DIR__, 1, 'backward', 2);
        $connection = $this->mockConnectionToJson($jsonFile);

        $slice = $connection->readStreamEventsBackward('test', 0, 2, false);
        $events = $slice->getEvents();

        $expected = [
            new ReadEvent('SomethingElseHappened', ['bar' => 'buzz'], 1),
            new ReadEvent('SomethingHappened', ['foo' => 'fizz'], 0),
        ];

        $this->assertEquals($expected, $events);
    }

    public function testRead404()
    {
        $guzzle = $this->buildMockClient(function () {
            return new Response(404);
        });

        $connection = Connection::create(['client' => $guzzle]);

        $slice = $connection->readStreamEventsForward('test', 0, 2, false);

        $this->assertEquals('StreamNotFound', $slice->getStatus());
    }

    public function testRead410()
    {
        $guzzle = $this->buildMockClient(function () {
            return new Response(410);
        });

        $connection = Connection::create(['client' => $guzzle]);

        $slice = $connection->readStreamEventsForward('test', 0, 2, false);

        $this->assertEquals('StreamDeleted', $slice->getStatus());
    }

    /**
     * @expectedException EventStore\Exception\TransportException
     */
    public function testRead500ThrowsTransportException()
    {
        $guzzle = $this->buildMockClient(function () {
            return new Response(500);
        });

        $connection = Connection::create(['client' => $guzzle]);
        $connection->readStreamEventsForward('test', 0, 2, false);
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

        return Connection::create(['client' => $guzzle]);
    }

    /**
     * @param string   $direction
     * @param int      $start
     * @param int      $count
     * @param int|null $nextEvent
     */
    private function readStreamCommonAssertions($direction, $start, $count, $nextEvent = null)
    {
        $jsonFile = sprintf('%s/%d_%s_%d.json', __DIR__, $start, $direction, $count);
        $connection = $this->mockConnectionToJson($jsonFile);

        $method = 'readStreamEvents'.ucfirst($direction);

        /** @var StreamEventsSlice $slice */
        $slice = $connection->$method('test', $start, $count, false);

        $this->assertNotNull($this->request);

        $resource = sprintf('/streams/test/%d/%s/%d?embed=body', $start, $direction, $count);
        $this->assertEquals($resource, $this->request->getResource());
        $this->assertEquals('application/vnd.eventstore.atom+json', $this->request->getHeader('accept'));

        $this->assertInstanceOf('EventStore\StreamEventsSlice', $slice);
        $this->assertSame($direction, $slice->getReadDirection(), 'Read direction should match');
        $this->assertSame($start, $slice->getFromEventNumber(), 'FromEventNumber should match');
        $this->assertSame($nextEvent, $slice->getNextEventNumber(), 'NextEventNumber should match');
    }
}
