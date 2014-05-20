<?php
namespace EventStore;

/**
 * Class ReadEvent
 * @package EventStore
 */
class ReadEvent
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $version;

    /**
     * @param string $type
     * @param array  $data
     * @param int    $version
     */
    public function __construct($type, array $data, $version)
    {
        $this->type = $type;
        $this->data = $data;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
