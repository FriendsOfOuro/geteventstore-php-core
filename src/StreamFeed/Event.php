<?php
namespace EventStore\StreamFeed;

/**
 * Class Event
 * @package EventStore\StreamFeed
 */
final class Event
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $version;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @param string  $type
     * @param integer $version
     * @param array   $data
     * @param array   $metadata
     */
    public function __construct($type, $version, array $data, array $metadata = null)
    {
        $this->type = $type;
        $this->version = (integer) $version;
        $this->data = $data;
        $this->metadata = $metadata;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
