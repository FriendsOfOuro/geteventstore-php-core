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
     * @param  int                $expectedVersion
     * @return AppendEventCommand
     */
    public function create($eventType, array $data, $expectedVersion = AppendEventCommand::VERSION_ANY)
    {
        return new AppendEventCommand(Uuid::uuid4()->toString(), $eventType, $data, $expectedVersion);
    }
}
