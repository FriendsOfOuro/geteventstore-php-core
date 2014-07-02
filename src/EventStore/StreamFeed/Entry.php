<?php

namespace EventStore\StreamFeed;

final class Entry
{
    use HasLinks;

    private $json;

    public function __construct($json)
    {
       $this->json = $json;
    }

    public function getEventUrl()
    {
        $alternate = $this->getLinkUrl(LinkRelation::ALTERNATE());

        return $alternate;
    }

    protected function getLinks()
    {
        return $this->json['links'];
    }
}
