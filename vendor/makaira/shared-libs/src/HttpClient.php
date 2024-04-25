<?php

namespace Makaira;

abstract class HttpClient
{
    /**
     * Execute a HTTP request to the remote server
     *
     * @param string $method
     * @param string $url
     * @param mixed $body
     * @param array $headers
     * @return HttpClient\Response
     */
    abstract public function request($method, $url, $body = null, array $headers = array());
}
