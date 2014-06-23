<?php

namespace EventStore;

use EventStore\Exception\ConnectionFailedException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Request;

final class EventStore {
    private $url;
    private $httpClient;
    private $lastResponse;

    public function __construct($url) {
        $this->url = $url;

        $this->httpClient = new Client();
        $this->checkConnection();
    }

    public function writeToStream($stream_name, WritableToStream $events) {
        if($events instanceof Event) {
            $events = new EventCollection([$events]);
        }

        $request = $this->httpClient->createRequest('POST', $this->getStreamUrl($stream_name), ['json' => $events->toStreamData()]);
        $this->sendRequest($request);
    }

    public function getLastResponse() {
        return $this->lastResponse;
    }

    private function getStreamUrl($stream_name) {
        return sprintf('%s/streams/%s', $this->url, $stream_name);
    }

    private function sendRequest(Request $request) {
        $this->lastResponse = $this->httpClient->send($request);
    }

    private function checkConnection() {
        try {
            $request = $this->httpClient->createRequest('GET', $this->url);
            $this->sendRequest($request);
        } catch(RequestException $e){
            throw new ConnectionFailedException($e->getMessage());
        }
    }

}