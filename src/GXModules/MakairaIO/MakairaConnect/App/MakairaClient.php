<?php

namespace GXModules\MakairaIO\MakairaConnect\App;

use Gambio\Core\Configuration\Services\ConfigurationService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\MakairaIO\MakairaConnect\App\Core\RequestBuilder;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaCategory;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaProduct;
use Psr\Http\Message\ResponseInterface;

class MakairaClient
{
    private string $nonce = '';

    private Client $client;

    private string $makairaUrl;

    private string $makairaSecret;

    private string $makairaInstance;

    private ModuleConfigService $moduleConfigService;

    private string $language = 'de';

    private MakairaLogger $logger;

    public function __construct(ConfigurationService $configurationFinder)
    {
        $this->moduleConfigService = new ModuleConfigService($configurationFinder);
        $this->makairaUrl = $this->moduleConfigService->getMakairaUrl();
        $this->makairaSecret = $this->moduleConfigService->getMakairaSecret();
        $this->makairaInstance = $this->moduleConfigService->getMakairaInstance();
        $this->nonce = bin2hex(random_bytes(8));

        $this->language = $_SESSION['language_code'] ?? 'de';

        $this->client = new Client([
            'base_uri' => rtrim($this->makairaUrl, '/'), // we trim the url to make sure we have no double slashes
            'headers' => [
                'X-Makaira-Instance' => $this->makairaInstance,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function setLogger(MakairaLogger $logger) {
        $this->logger = $logger;
    }

    private function getHash($body): string
    {
        return hash_hmac(
            'sha256',
            $this->nonce.':'.json_encode($body),
            $this->makairaSecret
        );
    }

    private function getHeaders($body): array
    {
        return [
            'X-Makaira-Nonce' => $this->nonce,
            'X-Makaira-Hash' => $this->getHash($body),
            'Content-Type' => 'application/json',
        ];
    }

    public function doRequest($method, $url, $body = '')
    {
        return $this->client->request($method, $url, [
            'headers' => $this->getHeaders($body),
            'json' => $body,
        ]);
    }

    public function pushRevisionsAsync(array $revisions): \GuzzleHttp\Promise\PromiseInterface
    {
        return $this->client->putAsync('persistence/revisions', [
           'headers' => $this->getHeaders($revisions),
           'json' => $revisions,
        ]);
    }

    public function pushRevision(array $document): ResponseInterface|array
    {
        return $this->doRequest('PUT', 'persistence/revisions', $document);
    }

    public function rebuild(array $types)
    {
        $body = [
            'docTypes' => $types,
        ];

        return $this->doRequest('POST', 'persistence/revisions/rebuild', $body);
    }

    public function switch(array $types)
    {
        $body = [
            'docTypes' => $types,
        ];

        return $this->doRequest('POST', 'persistence/revisions/switch', $body);
    }

    public function getPublicFields(): \Psr\Http\Message\ResponseInterface
    {
        $query = [
            '_start' => 0,
            '_end' => 100,
            '_sort' => 'changed',
            '_order' => 'DESC',
        ];

        return $this->doRequest('GET', 'publicfield?'.implode('&', $query));
    }

    public function setPublicField(
        string $field,
        bool $showOnDetailPage = true,
        bool $showOnLandingPage = true,
        bool $showOnListingPage = true
    ): \Psr\Http\Message\ResponseInterface {
        return $this->doRequest('POST', 'publicfield', [
            'field' => $field,
            'fieldName' => $field,
            'fieldId' => 'new',
            'fieldType' => 'field',
            'onDetailPage' => $showOnDetailPage,
            'onLandingPage' => $showOnLandingPage,
            'onListingPage' => $showOnListingPage,
        ]);
    }

    public function getImporter(): ResponseInterface
    {
        return $this->doRequest('GET', 'importer/configs');
    }

    public function createImporter(): ResponseInterface
    {
        return $this->doRequest('POST', 'importer/config', [
            'internalTitle' => 'MakairaConnect Importer',
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
                    'kind' => 'continuously',
                    'timeout' => 21600,
                ],
                [
                    'active' => false,
                    'autoswitch' => true,
                    'bulk' => true,
                    'cronExpression' => '',
                    'kind' => 'one-time',
                    'timeout' => 21600,
                ],
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
                    'kind' => 'continuously',
                    'timeout' => 21600,
                ],
                [
                    'active' => false,
                    'autoswitch' => true,
                    'bulk' => true,
                    'cronExpression' => '',
                    'kind' => 'one-time',
                    'timeout' => 21600,
                ],
            ],
            'sourceAuthPassword' => '',
            'sourceAuthUser' => '',
            'sourceSecret' => $this->makairaSecret,
            'sourceType' => 'persistence-layer',
            'sourceUrl' => '',
            'targetType' => 'makaira',
        ]);
    }

    public function getManufacturer(string $id)
    {
        $requestBuilder = new RequestBuilder($this->language);

        $body = [
            'searchPhrase' => $id,
            'isSearch' => false,
            'enableAggregations' => false,
            'aggregations' => [],
            'sorting' => [
                'id' => 'ASC',
            ],
            'fields' => [],
            'count' => 0,
            'offset' => 0,
            'constraints' => $requestBuilder->getConstraint(),
        ];

        $url = $this->makairaUrl.'/search/public';

        return json_decode($this->doRequest('POST', $url, $body)->getBody()->getContents());
    }

    public function getProducts(string $categoryId, int $maxResults = 12, int $offset = 0, array $sorting = [], string $group = '', array $filter = [])
    {
        $requestBuilder = new RequestBuilder($this->language);

        $body = [
            'searchPhrase' => $categoryId,
            'isSearch' => false,
            'enableAggregations' => true,
            'aggregations' => $filter,
            'sorting' => $sorting,
            'fields' => [],
            'count' => $maxResults,
            'offset' => $offset,
            'constraints' => $requestBuilder->getConstraint(),
        ];

        $body['constraints']['query.categories_id'] = $categoryId;

        if (! empty($group)) {
            $body['constraints']['query.group'] = $group;
        }

        $url = $this->makairaUrl.'/search/';

        return json_decode($this->doRequest('POST', $url, $body)->getBody()->getContents(), true);
    }

    public function getCategory(string $id, int $maxSearchResults = 8, ?int $pageNumber = null)
    {
        if (empty($id)) {
            return [];
        }
        $requestBuilder = new RequestBuilder($this->language);

        $body = [
            'searchPhrase' => $id,
            'isSearch' => false,
            'enableAggregations' => true,
            'aggregations' => [],
            'sorting' => [],
            'fields' => array_merge(
                [
                    'subcategories',
                ],
                MakairaProduct::FIELDS,
                MakairaCategory::FIELDS,
            ),
            'count' => $maxSearchResults,
            'offset' => $pageNumber ?: 0,
            'constraints' => $requestBuilder->getConstraint(),
        ];

        $body['constraints']['query.categories_id'] = $id;

        $url = $this->makairaUrl.'/search/';

        return json_decode($this->doRequest('POST', $url, $body)->getBody()->getContents());
    }

    public function search(
        string $searchkey,
        int $maxSearchResults = 8,
        ?int $pageNumber = null,
        array $sorting = [],
        string $group = '',
        array $filter = []
    ) {
        if (empty($searchkey)) {
            return [];
        }
        $requestBuilder = new RequestBuilder($this->language);
        $body = [
            'searchPhrase' => $searchkey,
            'isSearch' => true,
            'enableAggregations' => true,
            'aggregations' => $filter,
            'sorting' => $sorting,
            'fields' => array_merge(
                [
                    'subcategories',
                ],
                MakairaProduct::FIELDS,
                MakairaCategory::FIELDS,
            ),
            'count' => $maxSearchResults,
            'offset' => $pageNumber ?: 0,
            'constraints' => $requestBuilder->getConstraint(),
        ];

        if (! empty($group)) {
            $body['constraints']['query.group'] = $group;
        }

        $url = $this->makairaUrl.'/search/';

        return json_decode($this->doRequest('POST', $url, $body)->getBody()->getContents(), true);
    }
}
