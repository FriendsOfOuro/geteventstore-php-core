<?php

namespace DB\EventStoreClient\Command;

use Rhumsaa\Uuid\Uuid;

/**
 * Class AppendEventCommandFactory
 * @package DB\EventStoreClient\Command
 */
class AppendEventCommandFactory
{
    /**
     * @param  string             $eventType
     * @param  array              $data
     * @return AppendEventCommand
     */
    public function create($eventType, array $data)
    {
        return new AppendEventCommand(Uuid::uuid4()->toString(), $eventType, $data);
    }
}
