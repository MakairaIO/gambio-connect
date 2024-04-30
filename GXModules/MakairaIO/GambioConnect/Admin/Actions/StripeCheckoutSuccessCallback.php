<?php

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Makaira\GambioConnect\Admin\Services\MakairaInstallationService;
use GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\GambioConnect\Admin\Services\StripeService;

class StripeCheckoutSuccessCallback extends AdminModuleAction
{
    protected ModuleConfigService $configurationService;

    public function __construct(
        protected Application $application,
    ) {
        $this->configurationService = new ModuleConfigService($this->application->get(ConfigurationService::class));
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $stripe = new StripeService();

        $checkoutSessionId = $this->configurationService->getStripeCheckoutId();
        $checkoutSession = $stripe->getCheckoutSession($checkoutSessionId);

        $this->configurationService->setStripeCheckoutEmail($checkoutSession->customer_details->email);

        $subdomain = $request->getUri()->getHost() === 'stage.makaira.io'
            ? 'gambio'
            : str_replace(['http://', 'https://', '.'], ['', '', '-'], $request->getUri()->getHost());

        $this->configurationService->setMakairaCronJobActive();

        $this->configurationService->setMakairaCronJobInterval();

        MakairaInstallationService::callInstallationService($this->configurationService, $subdomain, $this->url->base());

        return $response->withRedirect($this->url->admin() . '/makaira/account');
    }
}
