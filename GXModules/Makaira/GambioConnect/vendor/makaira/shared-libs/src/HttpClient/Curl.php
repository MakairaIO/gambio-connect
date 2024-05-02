<?php
/**
 * Created by PhpStorm.
 * User: support
 * Date: 09.08.17
 * Time: 16:37
 */

namespace Makaira\HttpClient;

use Makaira\HttpClient;
use Makaira\Exception as BaseException;
use Makaira\Exceptions\TimeoutException;

class Curl extends HttpClient
{
    /**
     * Optional default headers for each request.
     *
     * @var array
     */
    private $headers = [];

    /**
     * socket timeout in sec
     *
     * @var int
     */
    private $timeout;

    /**
     * establish connection timeout in sec
     *
     * @var int
     */
    private $connectTimeout;

    /**
     * socket timeout in msec
     *
     * @var int
     */
    private $timeoutMs;

    /**
     * establish connection timeout in msec
     *
     * @var int
     */
    private $connectTimeoutMs;

    /**
     * Curl constructor.
     *
     * @param int $timeout
     * @param int $connectTimeout
     */
    public function __construct($timeout = 1, $connectTimeout = 0)
    {
        $this->timeout        = $timeout;
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * Add default headers
     *
     * @param array $headers
     *
     * @return void
     */
    public function addDefaultHeaders(array $headers)
    {
        $this->headers = array_merge(
            $this->headers,
            $headers
        );
    }

    /**
     * @param int $timeoutMs
     */
    public function setTimeoutMs(int $timeoutMs)
    {
        $this->timeoutMs = $timeoutMs;
    }

    /**
     * @param int $connectTimeoutMs
     */
    public function setConnectTimeoutMs(int $connectTimeoutMs)
    {
        $this->connectTimeoutMs = $connectTimeoutMs;
    }

    /**
     * Execute a HTTP request to the remote server
     *
     * @param string $method
     * @param string $url
     * @param mixed  $body
     * @param array  $headers
     *
     * @return HttpClient\Response
     */
    public function request($method, $url, $body = null, array $headers = [])
    {
        $ch           = curl_init();
        $headerBuffer = fopen('php://memory', 'w+');

        if (0 < strlen((string) $body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($this->headers, $headers));
        curl_setopt($ch, CURLOPT_WRITEHEADER, $headerBuffer);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

        if (null === $this->timeoutMs) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeoutMs);
        }
        if (null === $this->connectTimeoutMs) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeoutMs);
        }

        $curlResponse = curl_exec($ch);

        if (false === $curlResponse) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);

            if (28 === $errno) {
                throw new TimeoutException("Connection to server '{$url}' timed out: " . $error);
            }

            throw new BaseException(
                "Could not connect to server '{$url}': " . $error
            );
        }

        $curlInfo = curl_getinfo($ch);
        curl_close($ch);

        fseek($headerBuffer, 0, SEEK_SET);
        $responseHeaders = stream_get_contents($headerBuffer);
        fclose($headerBuffer);

        $response       = new HttpClient\Response();
        $response->body = $curlResponse;

        $this->parseResponseHeaders($responseHeaders, $response);
        $response->status = $curlInfo['http_code'];
        $response->totalTime = $curlInfo['total_time'];

        return $response;
    }

    /**
     * @param $responseHeaders
     * @param $response
     */
    private function parseResponseHeaders($responseHeaders, $response)
    {
        $response->headers = [];
        $rawHeader         = trim($responseHeaders);

        foreach (explode("\r\n", $rawHeader) as $line) {
            if (0 == strlen($line)) {
                continue;
            }

            if (0 !== strpos($line, 'HTTP/') && !empty($line)) {
                $headerKeyValue = explode(':', $line, 2);
                if (!empty($headerKeyValue)) {
                    if (isset($headerKeyValue[1])) {
                        $response->headers[strtolower($headerKeyValue[0])] = ltrim($headerKeyValue[1]);
                    } else {
                        $response->headers[strtolower($headerKeyValue[0])] = '';
                    }
                }
            }
        }

        return $response;
    }
}
