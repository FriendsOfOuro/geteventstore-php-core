<?php
namespace EventStore;

use EventStore\ValueObjects\Identity\UUID;

/**
 * Class WritableEvent
 * @package EventStore
 */
final class WritableEvent implements WritableToStream
{
    /**
     * @var UUID
     */
    private $uuid;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @param  string        $type
     * @param  array         $data
     * @param  array         $metadata
     * @return WritableEvent
     */
    public static function newInstance($type, array $data, array $metadata = [])
    {
        return new self(new UUID(), $type, $data, $metadata);
    }

    /**
     * @param UUID   $uuid
     * @param string $type
     * @param array  $data
     * @param array  $metadata
     */
    public function __construct(UUID $uuid, $type, array $data, array $metadata = [])
    {
        $this->uuid = $uuid;
        $this->type = $type;
        $this->data = $data;
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function toStreamData()
    {
        return [
            'eventId'   => $this->uuid->toNative(),
            'eventType' => $this->type,
            'data'      => $this->data,
            'metadata'  => $this->metadata
        ];
    }
}
