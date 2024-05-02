<?php

namespace Makaira\HttpClient;

use Makaira\HttpClient;

class Signing extends HttpClient
{
    /**
     * @var HttpClient
     */
    private $aggregate;

    /**
     * @var string
     */
    private $sharedSecret;

    public function __construct(HttpClient $aggregate, $sharedSecret)
    {
        $this->aggregate = $aggregate;
        $this->sharedSecret = $sharedSecret;
    }

    /**
     * Execute a HTTP request to the remote server
     *
     * @param string $method
     * @param string $url
     * @param mixed $body
     * @param array $headers
     *
     * @return HttpClient\Response
     */
    public function request($method, $url, $body = null, array $headers = array())
    {
        $nonce = mt_rand(0, mt_getrandmax());
        $hash = hash_hmac('sha256', $nonce . ':' . $body, $this->sharedSecret);
        $headers[] = 'X-Makaira-Nonce: ' . $nonce;
        $headers[] = 'X-Makaira-Hash: ' . $hash;

        return $this->aggregate->request($method, $url, $body, $headers);
    }

    /**
     * @param int $timeoutMs
     */
    public function setTimeoutMs(int $timeoutMs)
    {
        $this->aggregate->setTimeoutMs($timeoutMs);
    }

    /**
     * @param int $connectTimeoutMs
     */
    public function setConnectTimeoutMs(int $connectTimeoutMs)
    {
        $this->aggregate->setConnectTimeoutMs($connectTimeoutMs);
    }
}
