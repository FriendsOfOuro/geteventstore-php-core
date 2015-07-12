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
     * @param string         $stream_name Name of the stream
     * @param StreamDeletion $mode        Deletion mode (soft or hard)
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
     * @param  StreamFeed      $stream_feed The stream feed to navigate through
     * @param  LinkRelation    $relation    The "direction" expressed as link relation
     * @return null|StreamFeed
     */
    public function navigateStreamFeed(StreamFeed $stream_feed, LinkRelation $relation)
    {
        $url = $stream_feed->getLinkUrl($relation);

        if (empty($url)) {
            return null;
        }

        return $this->readStreamFeed($url, $stream_feed->getEntryEmbedMode());
    }

    /**
     * Open a stream feed for read and navigation
     *
     * @param  string         $stream_name The stream name
     * @param  EntryEmbedMode $embed_mode  The event entries embed mode (none, rich or body)
     * @return StreamFeed
     */
    public function openStreamFeed($stream_name, EntryEmbedMode $embed_mode = null)
    {
        $url = $this->getStreamUrl($stream_name);

        return $this->readStreamFeed($url, $embed_mode);
    }

    /**
     * Read a single event
     *
     * @param  string $event_url The url of the event
     * @return Event
     */
    public function readEvent($event_url)
    {
        $request = $this->getJsonRequest($event_url);
        $this->sendRequest($request);

        $this->ensureStatusCodeIsGood($event_url);

        $jsonResponse = $this->lastResponse->json();

        return $this->createEventFromResponseContent($jsonResponse['content']);
    }

    /**
     * @param  array $content
     * @return Event
     */
    protected function createEventFromResponseContent(array $content)
    {
        $type = $content['eventType'];
        $version = (integer) $content['eventNumber'];
        $data = $content['data'];
        $metadata = (!empty($content['metadata'])) ? $content['metadata'] : NULL;

        return new Event($type, $version, $data, $metadata);
    }

    /**
     * Write one or more events to a stream
     *
     * @param  string                                  $stream_name     The stream name
     * @param  WritableToStream                        $events          Single event or a collection of events
     * @param  int                                     $expectedVersion The expected version of the stream
     * @throws Exception\WrongExpectedVersionException
     */
    public function writeToStream($stream_name, WritableToStream $events, $expectedVersion = ExpectedVersion::ANY)
    {
        if ($events instanceof WritableEvent) {
            $events = new WritableEventCollection([$events]);
        }

        $request = $this
            ->httpClient
            ->createRequest(
                'POST',
                $this->getStreamUrl($stream_name),
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
     * @param  string $stream_name
     * @return string
     */
    private function getStreamUrl($stream_name)
    {
        return sprintf('%s/streams/%s', $this->url, $stream_name);
    }

    /**
     * @param  string                            $stream_url
     * @param  EntryEmbedMode                    $embed_mode
     * @return StreamFeed
     * @throws Exception\StreamDeletedException
     * @throws Exception\StreamNotFoundException
     */
    private function readStreamFeed($stream_url, EntryEmbedMode $embed_mode = null)
    {
        $request = $this->getJsonRequest($stream_url);

        if ($embed_mode != null && $embed_mode != EntryEmbedMode::NONE()) {
            $request->getQuery()->add('embed', $embed_mode->toNative());
        }

        $this->sendRequest($request);

        $this->ensureStatusCodeIsGood($stream_url);

        $jsonResponse = $this->lastResponse->json();

        return new StreamFeed($jsonResponse, $embed_mode);
    }

    private function getJsonRequest($uri)
    {
        return $this
            ->httpClient
            ->createRequest(
                'GET',
                $uri,
                [
                    'headers' => [
                        'Accept' => 'application/vnd.eventstore.atom+json'
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
     * @param $stream_url
     * @throws Exception\StreamDeletedException
     * @throws Exception\StreamNotFoundException
     * @throws Exception\UnauthorizedException
     */
    private function ensureStatusCodeIsGood($stream_url)
    {
        $code = $this->lastResponse->getStatusCode();

        if (array_key_exists($code, $this->badCodeHandlers)) {
            $this->badCodeHandlers[$code]($stream_url);
        }
    }

    private function initBadCodeHandlers()
    {
        $this->badCodeHandlers = [
            ResponseCode::HTTP_NOT_FOUND => function ($stream_url) {
                    throw new StreamNotFoundException(
                        sprintf(
                            'No stream found at %s',
                            $stream_url
                        )
                    );
                },

            ResponseCode::HTTP_GONE => function ($stream_url) {
                    throw new StreamDeletedException(
                        sprintf(
                            'Stream at %s has been permanently deleted',
                            $stream_url
                        )
                    );
                },

            ResponseCode::HTTP_UNAUTHORIZED => function ($stream_url) {
                    throw new UnauthorizedException(
                        sprintf(
                            'Tried to open stream %s got 401',
                            $stream_url
                        )
                    );
                }
        ];
    }
}
