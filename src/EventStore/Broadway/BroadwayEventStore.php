<?php
namespace EventStore\Broadway;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainEventStreamInterface;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventStore\EventStoreInterface as BroadwayEventStoreInterface;
use Broadway\EventStore\EventStreamNotFoundException;
use EventStore\EventStoreInterface;
use EventStore\Exception\StreamNotFoundException;
use EventStore\Exception\WrongExpectedVersionException;
use EventStore\StreamFeed\LinkRelation;
use EventStore\WritableEvent;
use EventStore\WritableEventCollection;

class BroadwayEventStore implements BroadwayEventStoreInterface
{
    private $eventStore;

    public function __construct(EventStoreInterface $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * @inheritDoc
     */
    public function load($id)
    {
        try {
            $feed = $this
                ->eventStore
                ->openStreamFeed($id)
            ;
        } catch (StreamNotFoundException $e) {
            throw new EventStreamNotFoundException($e->getMessage());
        }

        $feed = $this
            ->eventStore
            ->navigateStreamFeed(
                $feed,
                LinkRelation::FIRST()
            )
        ;

        $rel = LinkRelation::NEXT();

        $messages = [];

        $i = 0;
        while ($feed !== null) {
            foreach ($feed->getEntries() as $entry) {
                $event = $this
                    ->eventStore
                    ->readEvent($entry->getEventUrl()
                );

                $data = $event->getData();
                $recordedOn = DateTime::fromString($data['broadway_recorded_on']);
                unset($data['broadway_recorded_on']);

                $messages[] = new DomainMessage(
                    $id,
                    $i++,
                    new MetaData([]),
                    call_user_func(
                        [
                            $entry->getType(),
                            'deserialize'
                        ],
                        $data
                    ),
                    $recordedOn
                );
            }

            $feed = $this
                ->eventStore
                ->navigateStreamFeed(
                    $feed,
                    $rel
                )
            ;
        }

        return new DomainEventStream($messages);
    }

    /**
     * @inheritDoc
     */
    public function append($id, DomainEventStreamInterface $eventStream)
    {
        $events = [];
        $playhead = null;

        foreach ($eventStream as $message) {
            $payload = $message->getPayload();

            if ($playhead === null) {
                $playhead = $message->getPlayhead();
            }

            $events[] = WritableEvent::newInstance(
                get_class($payload),
                array_merge(
                    $payload->serialize(),
                    [
                        'broadway_recorded_on' => $message
                            ->getRecordedOn()
                            ->toString()
                    ]
                )
            );
        }

        try {
            $this
                ->eventStore
                ->writeToStream(
                    $id,
                    new WritableEventCollection($events),
                    $playhead - 1
                )
            ;
        } catch (WrongExpectedVersionException $e) {
            throw new BroadwayOptimisticLockException($e->getMessage());
        }
    }
}
