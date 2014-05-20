<?php
namespace EventStore;

/**
 * Class BackwardReader
 * @package EventStore
 */
class BackwardReader extends Reader
{
    /**
     * @param array     $events
     * @param ReadEvent $event
     */
    protected function append(array &$events, ReadEvent $event)
    {
        $events[] = $event;
    }

    /**
     * @param  array $links
     * @return int
     */
    protected function getNextEventNumber(array $links)
    {
        foreach ($links as $link) {
            if ('next' === $link['relation']) {
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
            'backward',
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
        return sprintf('/streams/%s/%d/%s/%d', $stream, $start, 'backward', $count);
    }
}
