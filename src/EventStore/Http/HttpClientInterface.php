<?php
namespace EventStore\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @package EventStore\Http
 */
interface HttpClientInterface
{
    /**
     * @param  RequestInterface  $request
     * @return ResponseInterface
     */
    public function send(RequestInterface $request);
}
