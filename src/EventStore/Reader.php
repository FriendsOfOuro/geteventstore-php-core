<?php
namespace EventStore;

/**
 * Class Reader
 * @package EventStore
 */
abstract class Reader
{
    public function decodeEvents(array $entries, $readDirection)
    {
        $decoded = [];

        foreach ($entries as $entry) {
            $this->append($decoded, $this->decodeEvent($entry));
        }

        return $decoded;
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
     * @param  string $link
     * @return int
     */
    private function getVersion($link)
    {
        $parts = explode('/', $link['uri']);

        return (int) array_pop($parts);
    }
}
