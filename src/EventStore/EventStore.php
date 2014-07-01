<?php

namespace EventStore;

use EventStore\Exception\ConnectionFailedException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Class EventStore
 * @package EventStore
 */
final class EventStore
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var ResponseInterface
     */
    private $lastResponse;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;

        $this->httpClient = new Client();
        $this->checkConnection();
    }

    /**
     * @param string         $stream_name
     * @param StreamDeletion $mode
     */
    public function deleteStream($stream_name, StreamDeletion $mode)
    {
        $request = $this->httpClient->createRequest('DELETE', $this->getStreamUrl($stream_name));

        if ($mode == StreamDeletion::HARD) {
            $request->addHeader('ES-HardDelete', 'true');
        }

        $this->sendRequest($request);
    }

    /**
     * @return ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * @param  StreamFeed             $stream_feed
     * @param  StreamFeedLinkRelation $relation
     * @return StreamFeed
     */
    public function navigateStreamFeed(StreamFeed $stream_feed, StreamFeedLinkRelation $relation)
    {
        $url        = $stream_feed->getLinkUrl($relation);
        $streamFeed = $this->readStreamFeed($url, $stream_feed->getEventEmbedMode());

        return $streamFeed;
    }

    /**
     * @param  string         $stream_name
     * @param  EventEmbedMode $embed_mode
     * @return StreamFeed
     */
    public function openStreamFeed($stream_name, EventEmbedMode $embed_mode = null)
    {
        $url        = $this->getStreamUrl($stream_name);
        $streamFeed = $this->readStreamFeed($url, $embed_mode);

        return $streamFeed;
    }

    /**
     * @param string           $stream_name
     * @param WritableToStream $events
     */
    public function writeToStream($stream_name, WritableToStream $events)
    {
        if ($events instanceof WritableEvent) {
            $events = new WritableEventCollection([$events]);
        }

        $request = $this->httpClient->createRequest('POST', $this->getStreamUrl($stream_name), ['json' => $events->toStreamData()]);
        $this->sendRequest($request);
    }

    /**
     * @param  string $stream_name
     * @return string
     */
    private function getStreamUrl($stream_name)
    {
        return sprintf('%s/streams/%s', $this->url, $stream_name);
    }

    /**
     * @param RequestInterface $request
     */
    private function sendRequest(RequestInterface $request)
    {
        try {
            $this->lastResponse = $this->httpClient->send($request);
        } catch (ClientException $e) {
            $this->lastResponse = $e->getResponse();
        }
    }

    /**
     * @throws Exception\ConnectionFailedException
     */
    private function checkConnection()
    {
        try {
            $request = $this->httpClient->createRequest('GET', $this->url);
            $this->sendRequest($request);
        } catch (RequestException $e) {
            throw new ConnectionFailedException($e->getMessage());
        }
    }

    /**
     * @param  string         $stream_url
     * @param  EventEmbedMode $embed_mode
     * @return StreamFeed
     */
    private function readStreamFeed($stream_url, EventEmbedMode $embed_mode = null)
    {
        $request = $this->httpClient->createRequest('GET', $stream_url);
        $request->addHeader('Accept', 'application/json');

        if ($embed_mode != null && $embed_mode != EventEmbedMode::NONE()) {
            $request->getQuery()->add('embed', $embed_mode->toNative());
        }

        $this->sendRequest($request);

        $jsonResponse = $this->lastResponse->json();
        $streamFeed = new StreamFeed($jsonResponse, $embed_mode);

        return $streamFeed;
    }
}
