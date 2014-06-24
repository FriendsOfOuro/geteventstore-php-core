<?php

namespace EventStore;

use ValueObjects\Identity\UUID;

final class WritableEvent implements WritableToStream
{
    private $uuid;
    private $type;
    private $data;

    public static function newInstance($type, $data)
    {
        $uuid  = new UUID();
        $event = new self($uuid, $type, $data);

        return $event;
    }

    public function __construct(UUID $uuid, $type, $data)
    {
        $this->uuid = $uuid;
        $this->type = $type;
        $this->data = $data;
    }

    public function toStreamData()
    {
        return [
            'eventId'   => $this->uuid->toNative(),
            'eventType' => $this->type,
            'data'      => $this->data
        ];
    }

}
