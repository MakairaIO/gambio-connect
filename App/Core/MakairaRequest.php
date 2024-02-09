<?php

namespace GXModules\Makaira\GambioConnect\App\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;

class MakairaRequest
{
  private Client $client;
  private string $makairaUrl;
  private string $makairaInstance;
  private string $language;

  public function __construct(string $makairaUrl, string $makairaInstance, string $language)
  {
    $this->makairaInstance = $makairaInstance;
    $this->makairaUrl = $makairaUrl;
    $this->language = $language;

    $this->client = new Client([
      'base_uri' => rtrim($this->makairaUrl), // we trim the url to make sure we have no double slashes
      'headers' => [
        'X-Makaira-Instance' => $this->makairaInstance,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
      ]
    ]);
  }

  public function fetchPageData(string $url)
  {
    $requestBuilder = new RequestBuilder($this->language);
    $body = [
      'searchPhrase' => '',
      'isSearch' => false,
      'enableAggregations' => false,
      'url' => $url,
      'count' => 50,
      'offset' => 0,
      'constraints' => $requestBuilder->getConstraint()
    ];
    $uri = $this->makairaUrl . $this->getEndpoint('page');
    $response = $this->request('POST', $uri, $body);
    return $response;
  }

  public function fetchAutoSuggest(string $searchPhrase) {
    $requestBuilder = new RequestBuilder($this->language);
    $body = [
      'searchPhrase' => $searchPhrase,
      'isSearch' => true,
      'enableAggregations' => false,
      'aggregations' => [],
      'sorting' => [],
      'count' => 8,
      'offset' => 0,
      'constraints' => $requestBuilder->getConstraint()
    ];
    $uri = $this->makairaUrl . $this->getEndpoint('search');
    $response = $this->request('POST', $uri, $body);
    return $response;
  }
  
  public function fetchRecommendations(string $productId) {
      $requestBuilder = new RequestBuilder($this->language);
      $body = [
          'recommendationId' => 'similar-products',
          'productId' => [$productId],
          'count' => 6,
          'constraints' => $requestBuilder->getConstraint(),
          'boosting' => [],
          'filter' => [],
          'fields' => [
              'gm_alt_text',
              'title',
              'longdesc',
              'shortdesc',
              'picture_url_main',
              'fsk_18',
              'price',
              'products_vpe',
              'products_vpe_status',
              'products_vpe_value'
          ]
      ];
      $uri = $this->makairaUrl . $this->getEndpoint('recommendation');
      $response = $this->request('POST', $uri, $body);
      return $response;
  }

  public function getPageComponents($pageData) {
    return $pageData['data']['config']['top']['elements'];
  }

  private function getEndpoint($endPointType)
  {
    switch ($endPointType) {
      case 'search':
        return '/search/public';
      case 'snippets':
        return '/enterprise/snippets';
      case 'recommendation':
        return '/recommendation/public';
      case 'documents':
        return '/documents/public';
      default:
        return '/enterprise/page';
    }
  }

  private function request($method, $url, $body)
  {
    try {
      $options = [];
      if ($body) {
        $options['json'] = $body;
      }
      $response = $this->client->request($method, $url, $options);
      $body = $response->getBody()->getContents();
      return json_decode($body, true);
    } catch (ServerException $e) {
      throw new \Exception('Request failed: Response: ' . $url . json_encode($body) . $e->getMessage());
    }
  }
}
