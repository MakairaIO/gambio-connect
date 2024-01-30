<?php

namespace GXModules\Makaira\GambioConnect\Admin\Services;

use http\Client;
use Psr\Http\Message\ResponseInterface;

class MakairaInstallationService
{
    private const URL = 'https://registration.makaira.io/api';
    
    private const USERNAME = 'gambio';
    
    private const PASSWORD = '6hmtyEQEhecyN2oEDBrZ';
    
    private string $source = 'gambio';
    public function __construct(
        protected ?\GuzzleHttp\Client $client = null,
        private string $email = '',
        private string $subdomain = '',
        private string $shopUrl = '',
        private string $checkoutSessionId = ''
) {
        $this->client = new \GuzzleHttp\Client([
                                                   'base_url' => self::URL,
                                                   'headers'  => [
                                                       'Authorization' => 'BASIC ' . self::USERNAME . ' '
                                                                          . self::PASSWORD,
                                                   ],
                                               ]);
    }
    
    
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    
    public function setSubdomain(string $subdomain): void
    {
        $this->subdomain = $subdomain;
    }
    
    
    public function setShopUrl(string $shopUrl): void
    {
        $this->shopUrl = $shopUrl;
    }
    
    
    public function setCheckoutSessionId(string $checkoutSessionId): void
    {
        $this->checkoutSessionId = $checkoutSessionId;
    }
    
    public function callRegistrationService(): ResponseInterface
    {
        return $this->client->post('register', [
            'source' => $this->source,
            'subdomain' => $this->subdomain,
            'email' => $this->email,
            'shop_url' => $this->shopUrl,
            'checkout_id' => $this->checkoutSessionId
        ]);
    }
}