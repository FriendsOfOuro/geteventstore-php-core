<?php

namespace EventStore\Exception;

final class ConnectionFailedException extends \Exception {
    public function __construct($message) {
        $this->message = $message;
    }
}