<?php

namespace DB\EventStoreClient\Feed;

use GuzzleHttp\ClientInterface;
use Zend\Feed\Reader\Http\ClientInterface as FeedClientInterface;
use Zend\Feed\Reader\Http\ResponseInterface;

class FeedClient implements FeedClientInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Make a GET request to a given URI
     *
     * @param  string            $uri
     * @return ResponseInterface
     */
    public function get($uri)
    {
        return FeedResponse::fromGuzzleResponse($this->client->get($uri));
    }
}
