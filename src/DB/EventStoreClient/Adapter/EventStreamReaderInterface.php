<?php

namespace DB\EventStoreClient\Adapter;
use DB\EventStoreClient\Model\EventReference;

/**
 * Interface EventStreamReaderInterface
 * @package DB\EventStoreClient\Adapter
 */
interface EventStreamReaderInterface
{
    /**
     * @return void
     */
    public function load();

    /**
     * @return EventReference|null
     */
    public function getCurrent();
}
