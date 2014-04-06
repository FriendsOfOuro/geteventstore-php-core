<?php
namespace DB\EventStoreClient\Adapter;

use DB\EventStoreClient\Command\AppendEventCommand;
use DB\EventStoreClient\Model\EventReference;

/**
 * Class HttpEventStreamWriter
 * @package DB\EventStoreClient\Adapter
 */
interface EventStreamWriterInterface
{
    /**
     * @param  AppendEventCommand  $command
     * @return EventReference|null
     */
    public function applyAppend(AppendEventCommand $command);
}
