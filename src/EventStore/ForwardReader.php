<?php
namespace EventStore;

/**
 * Class ForwardReader
 * @package EventStore
 */
class ForwardReader extends Reader
{
    /**
     * @param array     $events
     * @param ReadEvent $event
     */
    protected function append(array &$events, ReadEvent $event)
    {
        array_unshift($events, $event);
    }

    /**
     * @param  array $links
     * @return int
     */
    protected function getNextEventNumber(array $links)
    {
        foreach ($links as $link) {
            if ('previous' === $link['relation']) {
                return $this->getVersion($link);
            }
        }
    }

    /**
     * @param  string            $status
     * @param  int               $start
     * @param  array             $events
     * @param  int               $nextEventNumber
     * @return StreamEventsSlice
     */
    protected function createStreamEventsSlice($status, $start, array $events, $nextEventNumber)
    {
        return new StreamEventsSlice(
            $status,
            $start,
            'forward',
            $events,
            $nextEventNumber
        );
    }

    /**
     * @param  string $stream
     * @param  int    $start
     * @param  int    $count
     * @return string
     */
    protected function getUri($stream, $start, $count)
    {
        return sprintf('/streams/%s/%d/%s/%d', $stream, $start, 'forward', $count);
    }
}
