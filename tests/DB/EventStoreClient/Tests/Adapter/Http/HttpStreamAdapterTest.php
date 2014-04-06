<?php

namespace DB\EventStoreClient\Tests\Adapter\Http;

use DB\EventStoreClient\Adapter\Http\HttpStreamAdapter;
use DB\EventStoreClient\Command\AppendEventCommandFactory;
use DB\EventStoreClient\Model\StreamReference;
use GuzzleHttp\Adapter\MockAdapter;
use GuzzleHttp\Adapter\TransactionInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\Response;

class HttpStreamAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var AppendEventCommandFactory
     */
    private $commandFactory;

    protected function setUp()
    {
        $this->commandFactory = new AppendEventCommandFactory();
    }

    protected function tearDown()
    {
        $this->request = null;
    }

    public function testAppendWorksProperly()
    {
        $client = $this->buildMockClient(function (TransactionInterface $trans) {
            return new Response(201);
        });

        $adapter = new HttpStreamAdapter($client, StreamReference::fromName('streamname'));
        $command = $this->commandFactory->create('event-type', ['foo' => 'bar']);

        $adapter->applyAppend($command);

        $this->assertInstanceOf('GuzzleHttp\\Message\\RequestInterface', $this->request);
        $this->assertEquals('/streams/streamname', $this->request->getResource());
        $this->assertEquals('POST', $this->request->getMethod());
    }

    public function testHeadersAndBodyAreCorrect()
    {
        $client = $this->buildMockClient(function (TransactionInterface $trans) {
            return new Response(201);
        });

        $adapter = new HttpStreamAdapter($client, StreamReference::fromName('streamname'));

        $command = $this->commandFactory->create('event-type', ['foo' => 'bar']);
        $adapter->applyAppend($command);

        $this->assertEquals('application/json', $this->request->getHeader('Content-type'));

        $expectedBody = json_encode([[
            'eventId'   => $command->getEventId(),
            'eventType' => $command->getEventType(),
            'data'      => $command->getData(),
        ]]);

        $this->assertJsonStringEqualsJsonString($expectedBody, (string) $this->request->getBody());
    }

    public function testApplyReturnsEventReference()
    {
        $client = $this->buildMockClient(function (TransactionInterface $trans) {
            $response = new Response(201);
            $response->setHeader('Location', 'http://127.0.0.1:2113/streams/streamname/10');

            return $response;
        });

        $streamName = 'streamname';
        $adapter = new HttpStreamAdapter($client, StreamReference::fromName($streamName));

        $command = $this->commandFactory->create('event-type', ['foo' => 'bar']);
        $reference = $adapter->applyAppend($command);

        $this->assertInstanceOf('DB\\EventStoreClient\\Model\\EventReference', $reference);

        $this->assertSame(10, $reference->getStreamVersion());
        $this->assertSame($streamName, $reference->getStreamReference()->getStreamName());
    }

    /**
     * this requirement was implicit in other tests not giving a Location
     * however I thing it's better to make this requirement explicit
     * @todo decide if it's better to throw an exception instead
     */
    public function testApplyWithNoLocationReturnsNull()
    {
        $client = $this->buildMockClient(function (TransactionInterface $trans) {
            return new Response(201);
        });

        $adapter = new HttpStreamAdapter($client, StreamReference::fromName('streamname'));

        $command = $this->commandFactory->create('event-type', ['foo' => 'bar']);
        $reference = $adapter->applyAppend($command);

        $this->assertNull($reference);
    }

    public function testExpectedVersionHeaderIsSet()
    {
        $client = $this->buildMockClient(function (TransactionInterface $trans) {
            return new Response(201);
        });

        $adapter = new HttpStreamAdapter($client, StreamReference::fromName('streamname'));

        $command = $this->commandFactory->create('event-type', ['foo' => 'bar'], 10);
        $adapter->applyAppend($command);

        $this->assertEquals(10, $this->request->getHeader('ES-ExpectedVersion'));
    }

    /**
     * @group end2end
     */
    public function testAppendCommandWithRealServer()
    {
        $client = new Client(['base_url' => 'http://127.0.0.1:2113/']);

        $adapter = new HttpStreamAdapter($client, StreamReference::fromName('streamname'));

        $command = $this->commandFactory->create('event-type', ['foo' => 'bar']);
        $adapter->applyAppend($command);
    }

    private function buildMockClient(callable $response)
    {
        $mockAdapter = new MockAdapter(function (TransactionInterface $trans) use ($response) {
            $this->request = $trans->getRequest();

            return $response($trans);
        });

        return new Client(['adapter' => $mockAdapter]);
    }
}
