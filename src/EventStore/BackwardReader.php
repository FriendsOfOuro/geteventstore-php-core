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
     * @return string
     */
    protected function getReadDirection()
    {
        return self::BACKWARD;
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
}
