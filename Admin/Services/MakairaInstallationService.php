<?php

namespace GXModules\Makaira\GambioConnect\Admin\Services;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class MakairaInstallationService
{
    private const URL = 'http://register.makaira.vm/api/register';
    
    //private const URL = 'http://register.makaira.vm/';
    
    private const USERNAME = 'gambio';
    
    private const PASSWORD = '6hmtyEQEhecyN2oEDBrZ';
    
    private string $source = 'gambio';
    
    
    public function __construct(
        protected ?Client $client = null,
        private string    $email = '',
        private string    $subdomain = '',
        private string    $shopUrl = '',
        private string    $checkoutSessionId = ''
    ) {
        $this->client = new Client([
                                       'base_uri' => self::URL,
                                       'headers'  => [
                                           'Authorization' => 'BASIC ' . self::USERNAME . ' ' . self::PASSWORD,
                                       ],
                                   ]);
    }
    
    
    public function setShopUrl(string $shopUrl): void
    {
        $this->shopUrl = $shopUrl;
    }
    
    
    public function setCheckoutSessionId(string $checkoutSessionId): void
    {
        $this->checkoutSessionId = $checkoutSessionId;
    }
    
    
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    
    public function setSubdomain(string $subdomain): void
    {
        $this->subdomain = $subdomain;
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
                                      'source'             => $this->source,
                                      'subdomain'          => $this->subdomain,
                                      'email'              => $this->email,
                                      'shop_url'           => $this->shopUrl,
                                      'stripe_checkout_id' => $this->checkoutSessionId,
                                      'callback_url'       => $this->callbackUri,
                                  ]),
        ]);
    }
}