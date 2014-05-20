<?php
namespace EventStore;

/**
 * Class BackwardReader
 * @package EventStore
 */
class BackwardReader extends Reader
{
    protected function append(array &$events, ReadEvent $event)
    {
        $events[] = $event;
    }
}
