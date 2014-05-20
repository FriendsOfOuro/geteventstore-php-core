<?php
namespace EventStore;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Class Reader
 * @package EventStore
 */
abstract class Reader
{
    const FORWARD = 'forward';
    const BACKWARD = 'backward';

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
     * @param  string            $stream
     * @param  int               $start
     * @param  int               $count
     * @param  bool              $resolveLinkTos
     * @return StreamEventsSlice
     */
    public function readStreamEvents($stream, $start, $count, $resolveLinkTos)
    {
        $url = \sprintf('/streams/%s/%d/%s/%d', $stream, $start, $this->getReadDirection(), $count);

        $response = $this->sendReadStreamEventsRequest($url);

        return $this->transformResponse($response, $start, $this->getReadDirection());
    }

    /**
     * @param  array $entries
     * @return array
     */
    public function decodeEvents(array $entries)
    {
        $decoded = [];

        foreach ($entries as $entry) {
            $this->append($decoded, $this->decodeEvent($entry));
        }

        return $decoded;
    }

    public function transformResponse(ResponseInterface $response, $start)
    {
        $data = $response->json();

        return new StreamEventsSlice(
            'Success',
            $start,
            $this->getReadDirection(),
            $this->decodeEvents($data['entries']),
            $this->getNextEventNumber($data['links'])
        );
    }

    /**
     * @param  array     $entry
     * @return ReadEvent
     */
    protected function decodeEvent(array $entry)
    {
        return new ReadEvent($entry['eventType'], json_decode($entry['data'], true), $this->getVersion($entry['links'][0]));
    }

    /**
     * @param array     $events
     * @param ReadEvent $event
     */
    abstract protected function append(array &$events, ReadEvent $event);

    /**
     * @return string
     */
    abstract protected function getReadDirection();

    /**
     * @param  array $links
     * @return int
     */
    abstract protected function getNextEventNumber(array $links);

    /**
     * @param  string $link
     * @return int
     */
    protected function getVersion($link)
    {
        $parts = explode('/', $link['uri']);

        return (int) array_pop($parts);
    }

    /**
     * @param $url
     * @return ResponseInterface
     */
    private function sendReadStreamEventsRequest($url)
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
}
