<?php

namespace EventStore;

use ValueObjects\Identity\UUID;

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
     * @param  string        $type
     * @param  array         $data
     * @return WritableEvent
     */
    public static function newInstance($type, $data)
    {
        $uuid  = new UUID();
        $event = new self($uuid, $type, $data);

        return $event;
    }

    /**
     * @param UUID $uuid
     * @param $type
     * @param $data
     */
    public function __construct(UUID $uuid, $type, $data)
    {
        $this->uuid = $uuid;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function toStreamData()
    {
        return [
            'eventId'   => $this->uuid->toNative(),
            'eventType' => $this->type,
            'data'      => $this->data
        ];
    }
}
