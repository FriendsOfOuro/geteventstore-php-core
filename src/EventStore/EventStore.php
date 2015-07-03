<?php

namespace EventStore;

use EventStore\Exception\ConnectionFailedException;
use EventStore\Exception\StreamDeletedException;
use EventStore\Exception\StreamNotFoundException;
use EventStore\Exception\UnauthorizedException;
use EventStore\Exception\WrongExpectedVersionException;
use EventStore\Http\ResponseCode;
use EventStore\StreamFeed\EntryEmbedMode;
use EventStore\StreamFeed\Event;
use EventStore\StreamFeed\LinkRelation;
use EventStore\StreamFeed\StreamFeed;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Class EventStore
 * @package EventStore
 */
final class EventStore implements EventStoreInterface
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
     * @var array
     */
    private $badCodeHandlers = [];

    /**
     * @param string $url Endpoint of the EventStore HTTP API
     */
    public function __construct($url)
    {
        $this->url = $url;

        $this->httpClient = new Client();
        $this->checkConnection();
        $this->initBadCodeHandlers();
    }

    /**
     * Delete a stream
     *
     * @param string         $streamName Name of the stream
     * @param StreamDeletion $mode       Deletion mode (soft or hard)
     */
    public function deleteStream($streamName, StreamDeletion $mode)
    {
        $request = $this->httpClient->createRequest('DELETE', $this->getStreamUrl($streamName));

        if ($mode == StreamDeletion::HARD) {
            $request->addHeader('ES-HardDelete', 'true');
        }

        $this->sendRequest($request);
    }

    /**
     * Get the response from the last HTTP call to the EventStore API
     *
     * @return ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Navigate stream feed through link relations
     *
     * @param  StreamFeed      $streamFeed The stream feed to navigate through
     * @param  LinkRelation    $relation   The "direction" expressed as link relation
     * @return null|StreamFeed
     */
    public function navigateStreamFeed(StreamFeed $streamFeed, LinkRelation $relation)
    {
        $url = $streamFeed->getLinkUrl($relation);

        if (empty($url)) {
            return null;
        }

        return $this->readStreamFeed($url, $streamFeed->getEntryEmbedMode());
    }

    /**
     * Open a stream feed for read and navigation
     *
     * @param  string         $streamName The stream name
     * @param  EntryEmbedMode $embedMode  The event entries embed mode (none, rich or body)
     * @return StreamFeed
     */
    public function openStreamFeed($streamName, EntryEmbedMode $embedMode = null)
    {
        $url = $this->getStreamUrl($streamName);

        return $this->readStreamFeed($url, $embedMode);
    }

    /**
     * Read a single event
     *
     * @param  string $eventUrl The url of the event
     * @return Event
     */
    public function readEvent($eventUrl)
    {
        $request = $this->getJsonRequest($eventUrl);
        $this->sendRequest($request);

        $this->ensureStatusCodeIsGood($eventUrl);

        $jsonResponse = $this->lastResponse->json();

        return new Event($jsonResponse);
    }

    /**
     * Write one or more events to a stream
     *
     * @param  string                                  $streamName      The stream name
     * @param  WritableToStream                        $events          Single event or a collection of events
     * @param  int                                     $expectedVersion The expected version of the stream
     * @throws Exception\WrongExpectedVersionException
     */
    public function writeToStream($streamName, WritableToStream $events, $expectedVersion = ExpectedVersion::ANY)
    {
        if ($events instanceof WritableEvent) {
            $events = new WritableEventCollection([$events]);
        }

        $request = $this
            ->httpClient
            ->createRequest(
                'POST',
                $this->getStreamUrl($streamName),
                [
                    'json' => $events->toStreamData(),
                ]
            )
        ;

        $request->setHeader('ES-ExpectedVersion', intval($expectedVersion));
        $request->setHeader('Content-Type', 'application/vnd.eventstore.events+json');

        $this->sendRequest($request);

        $responseStatusCode = $this->getLastResponse()->getStatusCode();

        if (ResponseCode::HTTP_BAD_REQUEST == $responseStatusCode) {
            throw new WrongExpectedVersionException();
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
     * @param  string $streamName
     * @return string
     */
    private function getStreamUrl($streamName)
    {
        return sprintf('%s/streams/%s', $this->url, $streamName);
    }

    /**
     * @param  string                            $streamUrl
     * @param  EntryEmbedMode                    $embedMode
     * @return StreamFeed
     * @throws Exception\StreamDeletedException
     * @throws Exception\StreamNotFoundException
     */
    private function readStreamFeed($streamUrl, EntryEmbedMode $embedMode = null)
    {
        $request = $this->getJsonRequest($streamUrl);

        if ($embedMode != null && $embedMode != EntryEmbedMode::NONE()) {
            $request->getQuery()->add('embed', $embedMode->toNative());
        }

        $this->sendRequest($request);

        $this->ensureStatusCodeIsGood($streamUrl);

        $jsonResponse = $this->lastResponse->json();

        return new StreamFeed($jsonResponse, $embedMode);
    }

    /**
     * @param  string                                       $uri
     * @return \GuzzleHttp\Message\Request|RequestInterface
     */
    private function getJsonRequest($uri)
    {
        return $this
            ->httpClient
            ->createRequest(
                'GET',
                $uri,
                [
                    'headers' => [
                        'Accept' => 'application/json'
                    ]
                ]
            )
        ;
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
     * @param  string                            $streamUrl
     * @throws Exception\StreamDeletedException
     * @throws Exception\StreamNotFoundException
     * @throws Exception\UnauthorizedException
     */
    private function ensureStatusCodeIsGood($streamUrl)
    {
        $code = $this->lastResponse->getStatusCode();

        if (array_key_exists($code, $this->badCodeHandlers)) {
            $this->badCodeHandlers[$code]($streamUrl);
        }
    }

    private function initBadCodeHandlers()
    {
        $this->badCodeHandlers = [
            ResponseCode::HTTP_NOT_FOUND => function ($streamUrl) {
                    throw new StreamNotFoundException(
                        sprintf(
                            'No stream found at %s',
                            $streamUrl
                        )
                    );
                },

            ResponseCode::HTTP_GONE => function ($streamUrl) {
                    throw new StreamDeletedException(
                        sprintf(
                            'Stream at %s has been permanently deleted',
                            $streamUrl
                        )
                    );
                },

            ResponseCode::HTTP_UNAUTHORIZED => function ($streamUrl) {
                    throw new UnauthorizedException(
                        sprintf(
                            'Tried to open stream %s got 401',
                            $streamUrl
                        )
                    );
                }
        ];
    }
}
