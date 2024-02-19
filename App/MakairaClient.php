<?php

namespace GXModules\Makaira\GambioConnect\App;

use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService;
use MainFactory;
use Psr\Http\Message\ResponseInterface;

class MakairaClient
{
    private string $nonce = "";
    private Client $client;
    private string $makairaUrl;
    private string $makairaSecret;
    private string $makairaInstance;
    private ModuleConfigService $moduleConfigService;

    public function __construct(ConfigurationService $configurationFinder)
    {

        $this->moduleConfigService = new ModuleConfigService($configurationFinder);
        $this->makairaUrl = $this->moduleConfigService->getMakairaUrl();
        $this->makairaSecret = $this->moduleConfigService->getMakairaSecret();
        $this->makairaInstance = $this->moduleConfigService->getMakairaInstance();
        $this->nonce = bin2hex(random_bytes(8));


        //  dump($this->options->get('makairaInstance'));

        $this->client = new Client([
            'base_uri' => rtrim($this->makairaUrl, "/"), // we trim the url to make sure we have no double slashes
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
            'content-type' => 'application/json'
        ];
    }

    public function do_request($method, $url, $body = '')
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
        return $this->do_request('PUT', 'persistence/revisions', $document);
    }

    public function rebuild(array $types)
    {
        $body = [
            'docTypes' => $types
        ];

        return $this->do_request('POST', 'persistence/revisions/rebuild', $body);
    }

    public function switch(array $types)
    {
        $body = [
            'docTypes' => $types
        ];

        return $this->do_request('POST', 'persistence/revisions/switch', $body);
    }

    public function getPublicFields(): \Psr\Http\Message\ResponseInterface
    {
        $query = [
            '_start' => 0,
            '_end' => 100,
            '_sort' => 'changed',
            '_order' => 'DESC'
        ];
        return $this->do_request('GET', 'publicfield?' . implode('&', $query));
    }

    public function setPublicField(string $field): \Psr\Http\Message\ResponseInterface
    {
        return $this->do_request('POST', 'publicfield', [
            'field' => $field,
            'fieldName' => $field,
            'fieldId' => 'new',
            'fieldType' => 'field',
            'onDetailPage' => true,
            'onLandingPage' => true,
            'onListingPage' => true,
        ]);
    }

    public function createImporter(): ResponseInterface
    {
        return $this->do_request('POST', 'importer/config', [
            'internalTitle' => 'GambioConnect Importer',
            'languages' => [],
            'notificationsEnabled' => false,
            'notificationMails' => [],
            'otherSchedules' => [
                [
                    'active' => true,
                    'autoswitch' => false,
                    'bulk' => true,
                    'cronExpression' => '',
                    'enabled' => true,
                    'kind' => 'continously',
                    'timeout' => 21600
                ],
                [
                    'active' => false,
                    'autoswitch' => true,
                    'bulk' => true,
                    'cronExpression' => '',
                    'kind' => 'one-time',
                    'timeout' => 21600
                ]
            ],
            'runnerCountMax' => 500,
            'scheduledImporters' => [],
            'schedules' => [
                [
                    'active' => true,
                    'autoswitch' => false,
                    'bulk' => true,
                    'cronExpression' => '',
                    'enabled' => true,
                    'kind' => 'continously',
                    'timeout' => 21600
                ],
                [
                    'active' => false,
                    'autoswitch' => true,
                    'bulk' => true,
                    'cronExpression' => '',
                    'kind' => 'one-time',
                    'timeout' => 21600
                ]
            ],
            'sourceAuthPassword' => '',
            'sourceAuthUser' => '',
            'sourceSecret' => $this->makairaSecret,
            'sourceType' => 'persistence-layer',
            'sourceUrl' => '',
            'targetType' => 'makaira'
        ]);
    }
}
