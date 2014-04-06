<?php

namespace DB\EventStoreClient\Adapter\Http;

use DB\EventStoreClient\Adapter\EventStreamReaderInterface;
use Zend\Feed\Reader\Feed\Atom;
use Zend\Feed\Reader\Reader;

/**
 * Class HttpEventStreamReader
 * @package DB\EventStoreClient\Adapter\Http
 */
class HttpEventStreamReader extends HttpEventStreamAdapter implements EventStreamReaderInterface
{
    /**
     * @var Atom
     */
    private $feed;

    /**
     * @return void
     */
    public function load()
    {
        $response = $this
            ->getClient()
            ->get($this->getStreamUri(), [
                'headers' => [
                    'Accept' => 'application/atom+xml'
                ]
            ])
        ;

        $this->feed = Reader::importString((string) $response->getBody());
    }

    /**
     * @return \DB\EventStoreClient\Model\EventReference|null
     */
    public function getCurrent()
    {
        return $this->locationToEventReference($this->feed->current()->getId());
    }
}
