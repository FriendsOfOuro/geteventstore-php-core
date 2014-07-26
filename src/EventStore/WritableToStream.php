<?php

namespace EventStore;

/**
 * Interface WritableToStream
 * @package EventStore
 */
interface WritableToStream
{
    /**
     * @return array
     */
    public function toStreamData();
}
