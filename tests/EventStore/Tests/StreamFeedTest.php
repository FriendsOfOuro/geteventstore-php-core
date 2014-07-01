<?php
namespace EventStore\Tests;

use EventStore\EventEmbedMode;
use EventStore\StreamFeed;
use EventStore\StreamFeedLinkRelation;

class StreamFeedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider modeProvider
     */
    public function event_embed_mode_is_returned_properly(EventEmbedMode $mode = null, EventEmbedMode $expected)
    {
        $feed = new StreamFeed([], $mode);

        $this->assertEquals($expected, $feed->getEventEmbedMode());
    }

    /**
     * @return array
     */
    public static function modeProvider()
    {
        return [
            [null, EventEmbedMode::NONE()],
            [$eem = EventEmbedMode::NONE(), $eem],
            [$eem = EventEmbedMode::RICH(), $eem],
            [$eem = EventEmbedMode::BODY(), $eem],
        ];
    }

    /**
     * @dataProvider relationProvider
     * @test
     */
    public function get_link_url_returns_proper_url(StreamFeedLinkRelation $relation)
    {
        $uri = 'http://sample.uri:12345/stream';

        $feed = new StreamFeed([
            'links' => [
                [
                    'relation' => (string) $relation,
                    'uri' => $uri
                ]
            ]
        ]);

        $this->assertEquals($uri, $feed->getLinkUrl($relation));
    }

    /**
     * @test
     */
    public function get_link_url_returns_null_on_missing_url()
    {
        $feed = new StreamFeed([
            'links' => [
                [
                    'relation' => 'first',
                    'uri' => 'http://sample.uri:12345/stream'
                ]
            ]
        ]);

        $this->assertNull($feed->getLinkUrl(StreamFeedLinkRelation::LAST()));
    }

    public static function relationProvider()
    {
        return [
            [StreamFeedLinkRelation::FIRST()],
            [StreamFeedLinkRelation::LAST()]
        ];
    }
}
