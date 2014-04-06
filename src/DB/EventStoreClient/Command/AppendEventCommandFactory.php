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
     * @param $eventType
     * @param  array              $data
     * @param $expectedVersion
     * @return AppendEventCommand
     */
    public function create($eventType, array $data, $expectedVersion = -2)
    {
        return new AppendEventCommand(Uuid::uuid4()->toString(), $eventType, $data, $expectedVersion);
    }
}
