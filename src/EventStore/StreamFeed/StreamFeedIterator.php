<?php
namespace EventStore\StreamFeed;

use ArrayIterator;
use EventStore\EventStoreInterface;

final class StreamFeedIterator implements \Iterator
{
    private $eventStore;

    private $streamName;

    private $feed;

    private $innerIterator;

    private $startingRelation;

    private $navigationRelation;

    private $arraySortingFunction;

    private function __construct(
        EventStoreInterface $eventStore,
        $streamName,
        LinkRelation $startingRelation,
        LinkRelation $navigationRelation,
        callable $arraySortingFunction
    )
    {
        $this->eventStore = $eventStore;
        $this->streamName = $streamName;
        $this->startingRelation = $startingRelation;
        $this->navigationRelation = $navigationRelation;
        $this->arraySortingFunction = $arraySortingFunction;
    }

    public static function forward(EventStoreInterface $eventStore, $streamName)
    {
        return new self(
            $eventStore,
            $streamName,
            LinkRelation::LAST(),
            LinkRelation::PREVIOUS(),
            'array_reverse'
        );
    }

    public static function backward(EventStoreInterface $eventStore, $streamName)
    {
        return new self(
            $eventStore,
            $streamName,
            LinkRelation::FIRST(),
            LinkRelation::NEXT(),
            function (array $array) { return $array; }
        );
    }

    public function current()
    {
        $entry = $this->innerIterator->current();

        return $this->eventStore->readEvent($entry->getEventUrl());
    }

    public function next()
    {
        $this->innerIterator->next();

        if (!$this->innerIterator->valid()) {
            $this->feed = $this
                ->eventStore
                ->navigateStreamFeed(
                    $this->feed,
                    $this->navigationRelation
                )
            ;

            $this->createInnerIterator();
        }
    }

    public function key()
    {
        return $this->innerIterator->current()->getTitle();
    }

    public function valid()
    {
        return $this->innerIterator->valid();
    }

    public function rewind()
    {
        $this->feed = $this->eventStore->openStreamFeed($this->streamName);

        if ($this->feed->hasLink($this->startingRelation)) {
            $this->feed = $this
                ->eventStore
                ->navigateStreamFeed(
                    $this->feed,
                    $this->startingRelation
                )
            ;
        }

        $this->createInnerIterator();
    }

    private function createInnerIterator()
    {
        if (null !== $this->feed) {
            $entries = $this->feed->getEntries();
        } else {
            $entries = [];
        }

        $this->innerIterator = new ArrayIterator(
            call_user_func(
                $this->arraySortingFunction,
                $entries
            )
        );
    }
}
