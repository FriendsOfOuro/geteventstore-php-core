<?php
namespace EventStore\Http;

use Exception as PhpException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\Pool;
use Psr\Http\Message\RequestInterface;

final class GuzzleHttpClient implements HttpClientInterface
{
    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?: new Client([
            'handler' => new CurlMultiHandler(),
        ]);
    }

    public function sendRequestBatch(array $requests)
    {
        $responses = Pool::batch(
            $this->client,
            $requests
        );

        foreach ($responses as $response) {
            if ($response instanceof PhpException) {
                throw $response;
            }
        }

        return $responses;
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
