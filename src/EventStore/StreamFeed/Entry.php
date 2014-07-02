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

    public function getJson()
    {
        return $this->json;
    }

}
