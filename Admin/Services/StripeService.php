<?php

namespace GXModules\Makaira\GambioConnect\Admin\Services;

use Gambio\Core\Configuration\Services\ConfigurationService;
use Stripe\Checkout\Session;

class StripeService
{
    public const BUNDLE_PRICE_ID = 'price_1OcknJKFggkIYTFugsFfdPwL';
    
    public const ADS_PRICE_ID = 'price_1OcklEKFggkIYTFu04T3hRGr';
    
    public const RECOMMENDATIONS_PRICE_ID = 'price_1ObNFhKFggkIYTFuh7BtXOSC';
    
    public const SEARCH_PRICE_ID = 'price_1OckhcKFggkIYTFu7IeFyRXI';
    
    public function __construct(
        private ?ConfigurationService $configurationService = null,
        private array $lineItems = [],
        private array $paymentMethodTypes = ['card'],
        private string $mode = 'subscription',
        private string $successUrl = 'http://0.0.0.0:2001/success',
        private string $cancelUrl = 'http://0.0.0.0:2001/cancel',
        private string $shopUrl = '',
    ) {
        \Stripe\Stripe::setApiKey('rk_test_51OZrRtKFggkIYTFu2fF8ez660T4WGFSR0Dke4BVPsu5JeJepy2paR1QhoMtGTdaoyeIg8Jny6FMCWVVlrlwXRyq000mY2tYjQM');
    }
    
    public function setConfigurationService(ConfigurationService $configurationService): static
    {
        $this->configurationService = $configurationService;
        
        return $this;
    }
    
    
    public function setPaymentMethodTypes(array $paymentMethodTypes): static
    {
        $this->paymentMethodTypes = $paymentMethodTypes;
        
        return $this;
    }
    
    
    public function setMode(string $mode): static
    {
        $this->mode = $mode;
        
        return $this;
    }
    
    
    public function setSuccessUrl(string $successUrl): static
    {
        $this->successUrl = $successUrl;
        
        return $this;
    }
    
    
    public function setCancelUrl(string $cancelUrl): static
    {
        $this->cancelUrl = $cancelUrl;
        
        return $this;
    }
    
    public function addPriceId(string $string): static {
        $this->lineItems[] = [
            'price' => $string,
            'quantity' => 1
        ];
        
        return $this;
    }
    
    public function setShopUrl(string $shopUrl): static {
        $this->shopUrl = $shopUrl;
        
        return $this;
    }
    
    public function getCheckoutSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }
    
    public function createCheckoutSession(): Session
    {
        $session = Session::create([
            'payment_method_types' => $this->paymentMethodTypes,
            'line_items' => $this->lineItems,
            'mode' => $this->mode,
            'success_url' => $this->successUrl,
            'cancel_url' => $this->cancelUrl,
            'metadata' => [
                'shop_url' => $this->shopUrl
            ]
                               ]);
        
        $this->configurationService->save('modules/MakairaGambioConnect/stripeCheckoutSession', $session->id);

        return $session;
    }
}