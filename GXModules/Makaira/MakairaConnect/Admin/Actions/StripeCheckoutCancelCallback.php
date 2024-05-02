<?php

namespace GXModules\Makaira\MakairaConnect\Admin\Actions;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Makaira\MakairaConnect\Admin\Services\ModuleConfigService;

class StripeCheckoutCancelCallback extends AbstractAction
{
    protected ModuleConfigService $moduleConfigService;

    public function __construct(
        protected Application $application,
    ) {
        $this->moduleConfigService = $this->application->get(ModuleConfigService::class);
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $this->moduleConfigService->setStripeCheckoutId();

        return $response->withJson(['success' => true]);
    }
}
