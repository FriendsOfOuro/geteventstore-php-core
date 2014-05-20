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
     * @return string
     */
    protected function getReadDirection()
    {
        return self::FORWARD;
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
}
