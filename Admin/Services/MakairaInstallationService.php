<?php

namespace GXModules\Makaira\GambioConnect\Admin\Services;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class MakairaInstallationService
{
    private const URL = 'https://registration.makaira.io/';

    //private const URL = 'http://register.makaira.vm/';

    private const USERNAME = 'gambio';

    private const PASSWORD = '73P9gKB7KR8DA8KxNHbm';

    private string $source = 'gambio';


    public function __construct(
        protected ?ModuleConfigService $moduleConfigService = null,
        protected ?Client $client = null,
        private string $email = '',
        private string $subdomain = '',
        private string $shopUrl = '',
        private string $checkoutSessionId = '',
        private string $callbackUri = '',
        private array $options = []
    ) {
        $this->client = new Client([
                                       'base_uri' => self::URL,
                                       'auth' => [
                                           self::USERNAME,
                                           self::PASSWORD,
                                           'basic'
                                       ]
                                   ]);
    }

    public static function callInstallationService(ModuleConfigService $moduleConfigService, string|null $subdomain = null, string|null $baseUrl = null): void
    {
        if ($moduleConfigService->shouldMakairaInstallationServiceBeCalled()) {
            $instance = new self();

            if (!$subdomain || !$baseUrl) {
                $data = $moduleConfigService->getMakairaInstallationServiceRequestData();
                $instance->setRequestDataArray($data);
            } else {
                $checkoutSessionId = $moduleConfigService->getStripeCheckoutId();

                $stripe = new StripeService();

                $checkoutSession = $stripe->getCheckoutSession($checkoutSessionId);

                $email = $checkoutSession->customer_details->email;
                $instance->setEmail($email);
                $instance->setCheckoutSessionId($checkoutSessionId);
                $instance->setShopUrl($baseUrl);
                $instance->setSubdomain(strtolower($subdomain));
                $instance->setCallbackUri($baseUrl . '/shop.php?do=MakairaInstallationService');
                $instance->setOptions([
                    'instance_name' => strtolower($subdomain)
                ]);
                $moduleConfigService->setMakairaInstallationServiceRequestData($instance->getRequestDataArray());
            }
            $instance->callRegistrationService();

            $moduleConfigService->setMakairaInstallationServiceCalled();
        }
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

    public function setOptions(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getRequestDataArray(): array
    {
        return [
            'source'             => $this->source,
            'subdomain'          => $this->subdomain,
            'email'              => $this->email,
            'shop_url'           => $this->shopUrl,
            'callback_url'       => $this->callbackUri,
            'options'            => $this->options,
            'payment'            => [
                'provider'       => 'stripe',
                'checkout_id'    => $this->checkoutSessionId
            ]
        ];
    }

    public function setRequestDataArray(array $data): static
    {
        $this->source = $data['source'];

        $this->subdomain = $data['subdomain'];

        $this->email = $data['email'];

        $this->shopUrl = $data['shop_url'];

        $this->callbackUri = $data['callback_url'];

        $this->options = $data['options'];

        $this->checkoutSessionId = $data['payment']['checkout_id'];

        return $this;
    }


    public function callRegistrationService(): ResponseInterface
    {
        return $this->client->post('/api/register', [
            'body' => json_encode($this->getRequestDataArray()),
        ]);
    }
}
