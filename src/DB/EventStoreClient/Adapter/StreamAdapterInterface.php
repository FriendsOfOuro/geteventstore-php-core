<?php
namespace DB\EventStoreClient\Adapter;

use DB\EventStoreClient\Command\AppendEventCommand;
use DB\EventStoreClient\Model\EventReference;

/**
 * Class HttpStreamAdapter
 * @package DB\EventStoreClient\Adapter
 */
interface StreamAdapterInterface
{
    /**
     * @param  AppendEventCommand  $command
     * @return EventReference|null
     */
    public function applyAppend(AppendEventCommand $command);
}
