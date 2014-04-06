<?php

namespace DB\EventStoreClient\Adapter\Http;

use DB\EventStoreClient\Adapter\EventStreamWriterInterface;
use DB\EventStoreClient\Command\AppendEventCommand;
use DB\EventStoreClient\Model\EventReference;

/**
 * Class HttpEventStreamWriter
 * @package DB\EventStoreClient\Adapter
 */
class HttpEventStreamWriter extends HttpEventStreamAdapter implements EventStreamWriterInterface
{
    /**
     * @param  AppendEventCommand  $command
     * @return EventReference|null
     */
    public function applyAppend(AppendEventCommand $command)
    {
        $response = $this->sendAppendRequest($command);

        return $this->locationToEventReference($response->getHeader('Location'));
    }

    /**
     * @param  AppendEventCommand $command
     * @return array
     */
    private function commandToArray(AppendEventCommand $command)
    {
        return [
            'eventId' => $command->getEventId(),
            'eventType' => $command->getEventType(),
            'data' => $command->getData(),
        ];
    }

    /**
     * @param  AppendEventCommand $command
     * @return array
     */
    private function buildHeaders(AppendEventCommand $command)
    {
        $headers = [
            'Content-type' => 'application/json'
        ];

        if ($command->getExpectedVersion() !== -2) {
            $headers['ES-ExpectedVersion'] = $command->getExpectedVersion();
        }

        return $headers;
    }

    /**
     * @param  AppendEventCommand $command
     * @return string
     */
    private function buildBody(AppendEventCommand $command)
    {
        return json_encode([$this->commandToArray($command)]);
    }

    /**
     * @param  AppendEventCommand                    $command
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    private function sendAppendRequest(AppendEventCommand $command)
    {
        return $this
            ->getClient()
            ->post($this->getStreamUri(), [
                'headers' => $this->buildHeaders($command),
                'body' => $this->buildBody($command)
            ])
        ;
    }
}
