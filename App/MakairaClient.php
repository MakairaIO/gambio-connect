<?php

namespace GXModules\Makaira\GambioConnect\App;

use Gambio\Core\Configuration\Services\ConfigurationFinder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use MainFactory;

class MakairaClient
{
    private string $nonce = "";
    private Client $client;
    private string $makairaUrl;
    private string $makairaSecret;
    private string $makairaInstance;


    /**
     * @var ConfigurationFinder
     */
    private $configurationFinder;

    public function __construct(ConfigurationFinder $configurationFinder)
    {

        $this->configurationFinder = $configurationFinder;
        $this->makairaUrl =  $this->configurationFinder->get('modules/MakairaGambioConnect/makairaUrl', 'https://stage.makaira.io');
        $this->makairaSecret = $this->configurationFinder->get('modules/MakairaGambioConnect/makairaSecret', 'aAO3XD4D2FoGxGKCVz4t');
        $this->makairaInstance = $this->configurationFinder->get('modules/MakairaGambioConnect/makairaInstance', 'gambio');
        $this->nonce = bin2hex(random_bytes(8));


        //  dump($this->options->get('makairaInstance'));

        $this->client = new Client([
            'base_uri' => rtrim($this->makairaUrl, "/") . '/persistence/', // we trim the url to make sure we have no double slashes
            'headers' => [
                'X-Makaira-Instance' => $this->makairaInstance,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }


    private function get_hash($body): string
    {
        return hash_hmac(
            'sha256',
            $this->nonce . ':' . json_encode($body),
            $this->makairaSecret
        );
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

    public function push_revision(array $document)
    {
        return $this->do_request('PUT', 'revisions', $document);
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
