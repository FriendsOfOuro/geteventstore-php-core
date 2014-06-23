<?php

namespace EventStore;

final class Stream
{
    private $name;
    private $url;

    private function __construct() {}

    public static function fromDecodedJson($json)
    {
        $stream = new self();
        $stream->name = $json['streamId'];
        $stream->url  = $json['id'];

        return $stream;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
