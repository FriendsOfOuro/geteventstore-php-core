<?php

namespace DB\EventStoreClient\Feed;

use GuzzleHttp\Message\ResponseInterface;
use Zend\Feed\Reader\Http\ResponseInterface as FeedResponseInterface;

/**
 * Class FeedResponse
 * Wraps Guzzle response for Zend Feed client
 * @package DB\EventStoreClient\Feed
 */
class FeedResponse implements FeedResponseInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Constructor
     * @param ResponseInterface $response
     */
    private function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Retrieve the response body
     *
     * @return string
     */
    public function getBody()
    {
        return (string) $this->response->getBody();
    }

    /**
     * Retrieve the HTTP response status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @param  ResponseInterface $response
     * @return FeedResponse
     */
    public static function fromGuzzleResponse(ResponseInterface $response)
    {
        return new self($response);
    }
}
