<?php
namespace EventStore\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Psr\Http\Message\RequestInterface;

final class GuzzleHttpClient implements HttpClientInterface
{
    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?: new Client();
    }

    public function send(RequestInterface $request)
    {
        try {
            return $this->client->send($request);
        } catch (GuzzleClientException $e) {
            throw new Exception\ClientException($e->getMessage(), $e->getCode(), $e);
        } catch (GuzzleRequestException $e) {
            throw new Exception\RequestException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
