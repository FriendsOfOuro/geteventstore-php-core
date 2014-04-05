<?php

namespace DB\EventStoreClient\Tests;

use DB\EventStoreClient\Client;

/**
 * Client class for EventStore API
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->client = new Client();
    }
}
