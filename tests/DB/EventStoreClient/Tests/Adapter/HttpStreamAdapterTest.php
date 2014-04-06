<?php

namespace DB\EventStoreClient\Tests\Adapter;

use DB\EventStoreClient\Adapter\HttpStreamAdapter;
use DB\EventStoreClient\Command\AppendEventCommandFactory;
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

    protected function tearDown()
    {
        $this->request = null;
    }

    public function testAppendWorksProperly()
    {
        $mockAdapter = new MockAdapter(function (TransactionInterface $trans) {
            // You have access to the request
            $this->request = $trans->getRequest();
            // Return a response
            return new Response(201);
        });

        $client = new Client(['adapter' => $mockAdapter]);
        $adapter = new HttpStreamAdapter($client, 'streamname');

        $commandFactory = new AppendEventCommandFactory();
        $command = $commandFactory->create('event-type', ['foo' => 'bar']);

        $adapter->applyAppend($command);

        $this->assertInstanceOf('GuzzleHttp\Message\RequestInterface', $this->request);
        $this->assertEquals('/streams/streamname', $this->request->getResource());
        $this->assertEquals('POST', $this->request->getMethod());
    }
}
