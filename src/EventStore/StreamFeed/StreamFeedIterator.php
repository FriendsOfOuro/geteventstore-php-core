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

    private $rewinded;

    private $pagesLeft;

    private function __construct(
        EventStoreInterface $eventStore,
        $streamName,
        LinkRelation $startingRelation,
        LinkRelation $navigationRelation,
        callable $arraySortingFunction,
        $limit
    )
    {
        $this->eventStore = $eventStore;
        $this->streamName = $streamName;
        $this->startingRelation = $startingRelation;
        $this->navigationRelation = $navigationRelation;
        $this->arraySortingFunction = $arraySortingFunction;
        $this->pagesLeft = $limit;
    }

    public static function forward(EventStoreInterface $eventStore, $streamName, $limit = PHP_INT_MAX)
    {
        return new self(
            $eventStore,
            $streamName,
            LinkRelation::LAST(),
            LinkRelation::PREVIOUS(),
            'array_reverse',
            $limit
        );
    }

    public static function backward(EventStoreInterface $eventStore, $streamName, $limit = PHP_INT_MAX)
    {
        return new self(
            $eventStore,
            $streamName,
            LinkRelation::FIRST(),
            LinkRelation::NEXT(),
            function (array $array) { return $array; },
            $limit
        );
    }

    public function current()
    {
        return $this->innerIterator->current();
    }

    public function next()
    {
        $this->rewinded = false;
        $this->innerIterator->next();

        if (!$this->innerIterator->valid() && !$this->limitReached()) {
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
        return $this->innerIterator->current()->getEntry()->getTitle();
    }

    public function valid()
    {
        return $this->innerIterator->valid();
    }

    public function rewind()
    {
        if ($this->rewinded) {
            return;
        }

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

        $this->rewinded = true;
    }

    private function createInnerIterator()
    {
        if (null !== $this->feed) {
            $entries = $this->feed->getEntries();
        } else {
            $entries = [];
        }

        if (empty($entries)) {
            $this->innerIterator = new ArrayIterator([]);

            return;
        }

        $entries = call_user_func(
            $this->arraySortingFunction,
            $entries
        );

        $urls = array_map(
            function ($entry) {
                return $entry->getEventUrl();
            },
            $entries
        );

        $this->innerIterator = new ArrayIterator(
            array_map(
                function ($entry, $event) {
                    return new EntryWithEvent(
                        $entry,
                        $event
                    );
                },
                $entries,
                $this->eventStore->readEventBatch($urls)
            )
        );
    }

    private function limitReached()
    {
        return --$this->pagesLeft <= 0;
    }
}
