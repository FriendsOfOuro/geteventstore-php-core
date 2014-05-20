<?php

namespace EventStore;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Stream\Stream;

/**
 * Class Connection
 * @package EventStore
 */
class Connection implements ConnectionInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private static $defaultOptions = [
        'base_url' => 'http://127.0.0.1:2113/'
    ];

    /**
     * Constructor
     *
     * @param ClientInterface $client
     */
    protected function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function appendToStream($stream, $expectedVersion, array $events)
    {
        $eventsArray = [];

        foreach ($events as $event) {
            $eventsArray[] = $event->toArray();
        }

        $this
           ->client
           ->post('/streams/'.$stream, [
                'body' => Stream::factory(json_encode($eventsArray)),
                'headers' => [
                    'Content-type'       => 'application/json',
                    'ES-ExpectedVersion' => $expectedVersion
                ]
           ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function readStreamEventsForward($stream, $start, $count, $resolveLinkTos)
    {
        $url = \sprintf('/streams/%s/%d/forward/%d', $stream, $start, $count);

        $response = $this->readStreamEvents($url);

        return $this->transformResponse($response, $start, 'forward');
    }

    /**
     * {@inheritdoc}
     */
    public function readStreamEventsBackward($stream, $start, $count, $resolveLinkTos)
    {
        $url = \sprintf('/streams/%s/%d/backward/%d', $stream, $start, $count);

        $response = $this->readStreamEvents($url);

        return $this->transformResponse($response, $start, 'backward');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteStream($stream, $hardDelete = false)
    {
        $headers = [
            'Content-type' => 'application/json',
        ];

        if ($hardDelete) {
            $headers['ES-HardDelete'] = 'true';
        }

        $this
            ->client
            ->delete('/streams/'.$stream, [
                'headers' => $headers
            ])
        ;
    }

    /**
     * @param  array      $options
     * @return Connection
     */
    public static function create(array $options = [])
    {
        $options = array_merge(self::$defaultOptions, $options);

        if (!isset($options['client'])) {
            $client = new Client([
                'base_url' => $options['base_url'],
                'exceptions' => false,
            ]);

            $options['client'] = $client;
        }

        return new self($options['client']);
    }

    private function transformResponse(ResponseInterface $response, $start, $readDirection)
    {
        $data = $response->json();

        $index = $readDirection === 'forward' ? 'previous' : 'next';

        return new StreamEventsSlice(
            'Success',
            $start,
            $readDirection,
            [],
            $this->getNextEventNumber($data['links'], $index)
        );
    }

    /**
     * @param $url
     * @return ResponseInterface
     */
    private function readStreamEvents($url)
    {
        return $this->client
            ->get($url, [
                'headers' => [
                    'accept' => 'application/vnd.eventstore.atom+json',
                ],
                'query' => [
                    'embed' => 'body'
                ]
            ])
        ;
    }

    private function getNextEventNumber(array $links, $index)
    {
        foreach ($links as $link) {
            if ($index === $link['relation']) {
                $parts = explode('/', $link['uri']);

                return (int) array_pop($parts);
            }
        }
    }
}
