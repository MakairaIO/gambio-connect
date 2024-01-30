<?php

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Makaira\GambioConnect\Admin\Services\MakairaInstallationService;

class StripeCheckoutSuccessCallback extends AbstractAction
{

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        $installationService = new MakairaInstallationService();
        $installationService->setEmail($configurationService->find('modules/MakairaGambioConnect/stripeCheckoutEmail'));
        $installationService->setCheckoutSessionId($configurationService->find('modules/MakairaGambioConnect/stripeCheckoutSession'));
        $installationService->setShopUrl($request->getUri()->getHost());
        $installationService->setSubdomain($request->getUri()->getUserInfo());
        $installationService->callRegistrationService();
        
        return $response->withJson(['success' => true]);
    }
}