<?php

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Makaira\GambioConnect\Admin\Services\StripeService;
use LegacyDependencyContainer;

class MakairaCheckoutAction extends AbstractAction
{

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $stripeService = new StripeService();
        
        $configurationService = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        
        $stripeService->setConfigurationService($configurationService);
        
        foreach($request->getParsedBodyParam('priceIds') as $priceId) {
            $stripeService->addPriceId($priceId);
        }
        
        $stripeService->setSuccessUrl($request->getUri()->getHost() . '/admin/makaira/gambio-connect/stripe-checkout-success-callback');
        
        $stripeService->setCancelUrl($request->getUri()->getHost() . '/admin/makaira/gambio-connect/stripe-checkout-cancel-callback');
        
        $session = $stripeService->createCheckoutSession();
        
        return $response->withRedirect($session->url);
    }
}