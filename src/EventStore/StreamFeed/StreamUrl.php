<?php
namespace EventStore\StreamFeed;

final class StreamUrl
{
    private $url;

    /**
     * @param string $baseUrl
     * @param string $name
     */
    public static function fromBaseUrlAndName($baseUrl, $name)
    {
        $baseUrl = rtrim($baseUrl, '/');
        return new self("$baseUrl/streams/$name");
    }

    private function __construct($url)
    {
        $this->url = $url;
    }

    public function __toString()
    {
        return $this->url;
    }
}
