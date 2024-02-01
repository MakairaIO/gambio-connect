<?php

namespace GXModules\Makaira\GambioConnect\Admin\Services;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class MakairaInstallationService
{
    //private const URL = 'https://registration.makaira.io/';
    
    private const URL = 'http://register.makaira.vm/';
    
    private const USERNAME = 'gambio';
    
    private const PASSWORD = '6hmtyEQEhecyN2oEDBrZ';
    
    private string $source = 'gambio';
    
    
    public function __construct(
        protected ?Client $client = null,
        private string    $email = '',
        private string    $subdomain = '',
        private string    $shopUrl = '',
        private string    $checkoutSessionId = '',
        private string $callbackUri = '',
    ) {
        $this->client = new Client([
                                       'base_uri' => self::URL,
                                       'headers'  => [
                                           'Authorization' => 'Basic ' . self::USERNAME . ' ' . self::PASSWORD,
                                           'Content-Type' => 'application/json',
                                           'Accept' => 'application/json'
                                       ],
                                   ]);
    }
    
    
    public function setEmail(string $email): static
    {
        $this->email = $email;
        
        return $this;
    }
    
    
    public function setSubdomain(string $subdomain): static
    {
        $this->subdomain = $subdomain;
        
        return $this;
    }
    
    
    public function setShopUrl(string $shopUrl): static
    {
        $this->shopUrl = $shopUrl;
        
        return $this;
    }
    
    
    public function setCheckoutSessionId(string $checkoutSessionId): static
    {
        $this->checkoutSessionId = $checkoutSessionId;
        
        return $this;
    }
    
    public function setCallbackUri(string $callbackUri): static
    {
        $this->callbackUri = $callbackUri;
        
        return $this;
    }
    
    
    public function callRegistrationService(): ResponseInterface
    {
        return $this->client->post('/api/register', [
            'body' => json_encode([
                'source'      => $this->source,
                'subdomain'   => $this->subdomain,
                'email'       => $this->email,
                'shop_url'    => $this->shopUrl,
                'stripe_checkout_id' => $this->checkoutSessionId,
                'callback_url' => $this->callbackUri
            ]),
        ]);
    }
}