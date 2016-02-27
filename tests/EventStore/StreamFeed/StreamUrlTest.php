<?php
namespace EventStore\StreamFeed;

class StreamUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_be_built_from_base_url_and_name()
    {
        $url = StreamUrl::fromBaseUrlAndName(
            'http://foobar.com/',
            'gregorio'
        );

        $this->assertEquals(
            'http://foobar.com/streams/gregorio',
            $url->__toString()
        );
    }
}
