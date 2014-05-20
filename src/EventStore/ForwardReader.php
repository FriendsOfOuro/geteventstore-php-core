<?php
namespace EventStore;

/**
 * Class ForwardReader
 * @package EventStore
 */
class ForwardReader extends Reader
{
    protected function append(array &$events, ReadEvent $event)
    {
        array_unshift($events, $event);
    }
}
