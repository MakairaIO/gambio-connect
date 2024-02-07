<?php

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Makaira\GambioConnect\Admin\Services\MakairaInstallationService;
use GXModules\Makaira\GambioConnect\Admin\Services\StripeService;

class StripeCheckoutSuccessCallback extends AbstractAction
{
    protected ConfigurationService $configurationService;
    
    public function __construct(
        protected Application $application,
    ) {
        $this->configurationService = $this->application->get(ConfigurationService::class);
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $stripe = new StripeService();
        
        $checkoutSessionId = $this->configurationService->find('modules/MakairaGambioConnect/stripeCheckoutSession')?->value();
        $checkoutSession = $stripe->getCheckoutSession($checkoutSessionId);
        
        $email = $checkoutSession->customer_details->email;
        
        $this->configurationService->save('modules/MakairaGambioConnect/stripeCheckoutEmail', $email);
        
        $installationService = new MakairaInstallationService();
        $installationService->setEmail($email);
        $installationService->setCheckoutSessionId($checkoutSessionId);
        $installationService->setShopUrl($request->getUri()->getHost());
        $installationService->setSubdomain(explode('.', $request->getUri()->getHost())[0]);
        $installationService->setCallbackUri($this->url->base() .'shop.php?do=MakairaInstallationService');
        $installationService->callRegistrationService();
        
        return $response->withJson(['success' => true]);
    }
}