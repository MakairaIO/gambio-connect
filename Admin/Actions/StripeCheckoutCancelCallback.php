<?php

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;

class StripeCheckoutCancelCallback extends AbstractAction
{

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        $configurationService->delete('modules/MakairaGambioConnect/stripeCheckoutSession');
        return $response->withJson(['success' => true]);
    }
}