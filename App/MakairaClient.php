<?php

namespace GXModules\Makaira\GambioConnect\App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use MainFactory;

class MakairaClient
{
    private $options;
    private $nonce = "";
    private $client;

    public function __construct()
    {
        //        $this->options = MainFactory::create('GXModuleConfigurationStorage', 'Makaira/GambioConnect');
        $this->nonce = "1234";


        //  dump($this->options->get('makairaInstance'));

        $this->client = new Client([
            //   'base_uri' => rtrim($this->options->get('makairaUrl'), "/") . '/persistence/', // we trim the url to make sure we have no double slashes
            'base_uri' => rtrim("https://stage.makaira.io", "/") . '/persistence/', // we trim the url to make sure we have no double slashes
            'headers' => [
                'X-Makaira-Instance' => "gambio", // $this->options->get('makairaInstance'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }


    private function get_hash($body)
    {
        return  hash_hmac(
            'sha256',
            $this->nonce . ':' . json_encode($body),
            "aAO3XD4D2FoGxGKCVz4t"
        );    //    $this->options->get('makairaSecret')
    }

    private function getHeaders($body): array
    {
        return [
            'X-Makaira-Nonce' => $this->nonce,
            'X-Makaira-Hash' => $this->get_hash($body),
        ];
    }

    public function do_request($method, $url, $body)
    {
        try {
            return $this->client->request($method, $url, [
                'headers' => $this->getHeaders($body),
                'json' => $body,
            ]);
        } catch (ClientException $e) {
            throw new \Exception('Request failed: Response: ' . $e->getMessage());
        }
    }

    public function push_revision($document)
    {
        return $this->do_request('PUT', 'revisions', $document);
    }


    public function delete_revision($document)
    {
    }

    public function rebuild(array $types)
    {
        $body = [
            'docTypes' => $types
        ];

        return $this->do_request('POST', 'revisions/rebuild', $body);
    }

    public function switch(array $types)
    {
        $body = [
            'docTypes' => $types
        ];

        return $this->do_request('POST', 'revisions/switch', $body);
    }
}
