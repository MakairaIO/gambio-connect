<?php

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;

class StripeCheckoutCancelCallback extends AbstractAction
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
        $this->configurationService->delete('modules/MakairaGambioConnect/stripeCheckoutSession');
        return $response->withJson(['success' => true]);
    }
}