<?php

namespace EventStore\StreamFeed;

/**
 * Class Event
 * @package EventStore\StreamFeed
 */
class Event
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $version;

    /**
     * @param array $data
     * @param int   $version
     */
    public function __construct(array $data, $version)
    {
        $this->data = $data;
        $this->version = $version;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
