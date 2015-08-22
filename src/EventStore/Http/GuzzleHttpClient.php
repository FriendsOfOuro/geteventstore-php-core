<?php
namespace EventStore\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;

final class GuzzleHttpClient implements HttpClientInterface
{
    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?: new Client();
    }

    public function send(RequestInterface $request)
    {
        return $this->client->send($request);
    }
}
