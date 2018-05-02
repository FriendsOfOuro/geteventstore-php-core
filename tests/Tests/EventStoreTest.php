<?php
namespace EventStore\Tests;

use EventStore\EventStore;
use EventStore\Http\GuzzleHttpClient;
use EventStore\StreamDeletion;
use EventStore\StreamFeed\Entry;
use EventStore\StreamFeed\EntryEmbedMode;
use EventStore\StreamFeed\LinkRelation;
use EventStore\StreamFeed\StreamFeedIterator;
use EventStore\ValueObjects\Identity\UUID;
use EventStore\WritableEvent;
use EventStore\WritableEventCollection;
use PHPUnit\Framework\TestCase;

class EventStoreTest extends TestCase
{
    /**
     * @var EventStore
     */
    private $es;

    protected function setUp()
    {
        $uri = getenv('EVENTSTORE_URI') ?: 'http://127.0.0.1:2113';
        $httpClient = new GuzzleHttpClient();
        $this->es = new EventStore($uri, $httpClient);
    }

    /**
     * @test
     */
    public function client_successfully_connects_to_event_store()
    {
        $this->assertRegExp('/^[2-3][0-9]{2}$/', (string) $this->es->getLastResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function event_is_written_to_stream()
    {
        $this->prepareTestStream();

        $this->assertEquals('201', $this->es->getLastResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function version_is_provided_after_writing_to_stream()
    {
        $streamName = $this->prepareTestStream();
        $event = WritableEvent::newInstance('Foo', ['foo' => 'bar']);

        $version = $this->es->writeToStream($streamName, $event);
        $this->assertSame(1, $version);
    }

    /**
     * @test
     * @expectedException \EventStore\Exception\WrongExpectedVersionException
     */
    public function wrong_expected_version_should_throw_exception()
    {
        $streamName = $this->prepareTestStream();
        $event = WritableEvent::newInstance('Foo', ['foo' => 'bar']);

        $this->es->writeToStream($streamName, $event, 3);
    }

    /**
     * @test
     */
    public function stream_is_soft_deleted()
    {
        $streamName = $this->prepareTestStream();
        $this->es->deleteStream($streamName, StreamDeletion::SOFT());

        $this->assertEquals('204', $this->es->getLastResponse()->getStatusCode());

        // we try to write to a soft deleted stream...
        $this->es->writeToStream($streamName, WritableEvent::newInstance('Foo', ['bar']));

        // ..and we should expect a "201 Created" response
        $this->assertEquals('201', $this->es->getLastResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function stream_is_hard_deleted()
    {
        $streamName = $this->prepareTestStream();
        $this->es->deleteStream($streamName, StreamDeletion::HARD());

        $this->assertEquals('204', $this->es->getLastResponse()->getStatusCode());

        // we try to write to a hard deleted stream...
        $this->es->writeToStream($streamName, WritableEvent::newInstance('Foo', ['bar']));

        // ..and we should expect a "410 Stream deleted" response
        $this->assertEquals('410', $this->es->getLastResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function stream_feed_is_successfully_opened()
    {
        $streamName = $this->prepareTestStream();
        $streamFeed = $this->es->openStreamFeed($streamName);

        $json = $streamFeed->getJson();

        $this->assertEquals($streamName, $json['streamId']);
    }

    /**
     * @test
     * @expectedException \EventStore\Exception\ConnectionFailedException
     */
    public function unreacheable_event_store_throws_exception()
    {
        $httpClient = new GuzzleHttpClient();
        new EventStore('http://127.0.0.1:12345/', $httpClient);
    }

    /**
     * @test
     */
    public function event_data_is_embedded_with_body_mode()
    {
        $streamName = $this->prepareTestStream();
        $streamFeed = $this->es->openStreamFeed($streamName, EntryEmbedMode::BODY());

        $json = $streamFeed->getJson();

        $this->assertEquals(['foo' => 'bar'], json_decode($json['entries'][0]['data'], true));
    }

    /**
     * @test
     */
    public function event_stream_feed_head_returns_next_link()
    {
        $streamName = $this->prepareTestStream(40);

        $head = $this->es->openStreamFeed($streamName);
        $next = $this->es->navigateStreamFeed($head, LinkRelation::NEXT());

        $this->assertInstanceOf('EventStore\StreamFeed\StreamFeed', $next);
        $this->assertCount(20, $next->getJson()['entries']);
    }

    /**
     * @test
     */
    public function event_stream_feed_returns_entries()
    {
        $streamName = $this->prepareTestStream(40);
        $feed = $this->es->openStreamFeed($streamName);
        $entries = $feed->getEntries();

        $this->assertCount(20, $entries);
        $this->assertContainsOnlyInstancesOf('EventStore\StreamFeed\Entry', $entries);
    }

    /**
     * @test
     */
    public function get_single_event_from_event_stream()
    {
        $streamName = $this->prepareTestStream(1);
        $feed = $this->es->openStreamFeed($streamName);

        /** @var Entry $entry */
        list($entry) = $feed->getEntries();
        $eventUrl = $entry->getEventUrl();

        $event = $this->es->readEvent($eventUrl);

        $this->assertSame(0, $event->getVersion());
        $this->assertInstanceOf('EventStore\StreamFeed\Event', $event);
        $this->assertEquals(['foo' => 'bar'], $event->getData());
        $this->assertSame(null, $event->getMetadata());
    }

    /**
     * @test
     */
    public function get_single_event_with_provided_event_id()
    {
        $eventId = new UUID();
        $streamName = $this->prepareTestStream(1);

        $event = new WritableEvent($eventId, 'Foo', ['foo' => 'bar']);
        $this->es->writeToStream($streamName, $event);

        $feed = $this->es->openStreamFeed($streamName);
        /** @var Entry $entry */
        list($entry) = $feed->getEntries();
        $eventUrl = $entry->getEventUrl();
        $readEvent = $this->es->readEvent($eventUrl);

        if ('Linux-v3.0.5' === getenv('EVENT_STORE_VERSION')) {
            // EventId was introduced in version 3.1.0
            $this->assertNull($readEvent->getEventId());
        } else {
            $this->assertEquals($eventId, $readEvent->getEventId());
        }
    }

    /**
     * @test
     */
    public function get_event_batch_from_event_stream()
    {
        $streamName = $this->prepareTestStream(20);
        $feed = $this->es->openStreamFeed($streamName);

        $eventUrls = array_map(
            function (Entry $entry) {
                return $entry->getEventUrl();
            },
            $feed->getEntries()
        );

        $events = $this->es->readEventBatch($eventUrls);
        $this->assertNotEmpty($events);

        $i = 19;
        foreach ($events as $event) {
            $this->assertSame($i--, $event->getVersion());
            $this->assertInstanceOf('EventStore\StreamFeed\Event', $event);
            $this->assertEquals(['foo' => 'bar'], $event->getData());
            $this->assertSame(null, $event->getMetadata());
        }
    }

    /**
     * @test
     */
    public function get_single_event_with_metadata_from_event_stream()
    {
        $metadata = [
            'user' => 'akii',
        ];

        $streamName = $this->prepareTestStream(1, $metadata);
        $feed = $this->es->openStreamFeed($streamName);

        /** @var Entry $entry */
        list($entry) = $feed->getEntries();
        $eventUrl = $entry->getEventUrl();

        $event = $this->es->readEvent($eventUrl);

        $this->assertSame(0, $event->getVersion());
        $this->assertInstanceOf('EventStore\StreamFeed\Event', $event);
        $this->assertEquals(['foo' => 'bar'], $event->getData());
        $this->assertEquals($metadata, $event->getMetadata());
    }

    /**
     * @test
     */
    public function navigate_stream_using_missing_link_returns_null()
    {
        $streamName = $this->prepareTestStream(1);

        $head = $this->es->openStreamFeed($streamName);
        $next = $this->es->navigateStreamFeed($head, LinkRelation::NEXT());

        $this->assertNull($next);
    }

    /**
     * @test
     * @expectedException \EventStore\Exception\StreamNotFoundException
     */
    public function unexistent_stream_should_throw_not_found_exception()
    {
        $this->es->openStreamFeed('this-stream-does-not-exists');
    }

    /**
     * @test
     * @expectedException \EventStore\Exception\StreamDeletedException
     */
    public function deleted_stream_should_throw_an_exception()
    {
        $streamName = $this->prepareTestStream();
        $this->es->deleteStream($streamName, StreamDeletion::HARD());

        $this->es->openStreamFeed($streamName);
    }

    /**
     * @test
     * @expectedException \EventStore\Exception\UnauthorizedException
     * @expectedExceptionMessage Tried to open stream http://127.0.0.1:2113/streams/$et-Baz got 401
     */
    public function unauthorized_streams_throw_unauthorized_exception()
    {
        $this->es->openStreamFeed('$et-Baz');
    }

    /**
     * @test
     * @expectedException \EventStore\Exception\StreamDeletedException
     */
    public function fetching_event_of_a_deleted_stream_throws_an_exception()
    {
        $streamName = $this->prepareTestStream(1);
        $feed = $this->es->openStreamFeed($streamName);
        $entries = $feed->getEntries();
        $eventUrl = $entries[0]->getEventUrl();

        $this->es->deleteStream($streamName, StreamDeletion::HARD());

        $event = $this->es->readEvent($eventUrl);
    }

    /**
     * @test
     */
    public function it_can_create_a_forward_iterator()
    {
        $streamName = $this->prepareTestStream(1);

        $this->assertEquals(
            StreamFeedIterator::forward(
                $this->es,
                $streamName
            ),
            $this->es->forwardStreamFeedIterator($streamName)
        );
    }

    /**
     * @test
     */
    public function it_can_create_a_backward_iterator()
    {
        $streamName = $this->prepareTestStream(1);

        $this->assertEquals(
            StreamFeedIterator::backward(
                $this->es,
                $streamName
            ),
            $this->es->backwardStreamFeedIterator($streamName)
        );
    }

    /**
     * @test
     */
    public function it_can_process_the_all_stream_with_a_forward_iterator()
    {
        $client = new \GuzzleHttp\Client([
            'auth' => ['admin', 'changeit'],
            'handler' => new \GuzzleHttp\Handler\CurlMultiHandler(),
        ]);
        $httpClient = new GuzzleHttpClient($client);
        $this->es = new EventStore('http://127.0.0.1:2113', $httpClient);

        $this->prepareTestStream(1);
        $streamName = rawurlencode('$all');

        $this->assertGreaterThan(
            0,
            iterator_count($this->es->forwardStreamFeedIterator($streamName))
        );
    }

    /**
     * @param int   $length
     * @param array $metadata
     *
     * @return string
     */
    private function prepareTestStream($length = 1, $metadata = [])
    {
        $streamName = uniqid();
        $events = [];

        for ($i = 0; $i < $length; ++$i) {
            $events[] = WritableEvent::newInstance('Foo', ['foo' => 'bar'], $metadata);
        }

        $collection = new WritableEventCollection($events);
        $this->es->writeToStream($streamName, $collection);

        return $streamName;
    }
}
