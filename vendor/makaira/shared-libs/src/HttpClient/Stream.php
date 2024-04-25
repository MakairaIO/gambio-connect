<?php

namespace Makaira\HttpClient;

use Makaira\HttpClient;
use Makaira\Exception as BaseException;

/**
 * HTTP client implementation
 *
 */
class Stream extends HttpClient
{
    /**
     * Optional default headers for each request.
     *
     * @var array
     */
    private $headers = array();

    /**
     * socket timeout
     *
     * @var float
     */
    private $timeout;

    public function __construct($timeout = 1.0)
    {
        $this->timeout= $timeout;
    }

    /**
     * Add default headers
     *
     * @param array $headers
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
     * Execute a HTTP request to the remote server
     *
     * @param string $method
     * @param string $url
     * @param mixed $body
     * @param array $headers
     * @return Response
     */
    public function request($method, $url, $body = null, array $headers = array())
    {
        $httpFilePointer = @fopen(
            $url,
            'r',
            false,
            stream_context_create(
                array(
                    'http' => array(
                        'method'        => $method,
                        'content'       => $body,
                        'ignore_errors' => true,
                        'timeout'       => $this->timeout,
                        'header'        => implode(
                            "\r\n",
                            array_merge(
                                $this->headers,
                                $headers
                            )
                        ),
                    ),
                )
            )
        );

        if ($httpFilePointer === false) {
            $error = error_get_last();
            throw new BaseException(
                "Could not connect to server {$url}: " . $error['message']
            );
        }

        $response = new HttpClient\Response();
        while (!feof($httpFilePointer)) {
            $response->body .= fgets($httpFilePointer);
        }

        $metaData   = stream_get_meta_data($httpFilePointer);
        $rawHeaders = isset($metaData['wrapper_data']['headers']) ?
            $metaData['wrapper_data']['headers'] :
            $metaData['wrapper_data'];

        foreach ($rawHeaders as $lineContent) {
            if (preg_match('(^HTTP/(?P<version>\d+\.\d+)\s+(?P<status>\d+))S', $lineContent, $match)) {
                $response->status = (int) $match['status'];
            } else {
                list($key, $value) = explode(':', $lineContent, 2);
                $response->headers[strtolower($key)] = ltrim($value);
            }
        }

        return $response;
    }
}
