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
        $pageTitle = 'Makaira Gambio Connect - Successful Checkout';
        $templatePath = __DIR__ . '/../ui/template/stripe/success.html';

        $stripe = new StripeService();

        $checkoutSessionId = $this->configurationService->getStripeCheckoutId();
        $checkoutSession = $stripe->getCheckoutSession($checkoutSessionId);

        $this->configurationService->setStripeCheckoutEmail($checkoutSession->customer_details->email);

        $subdomain = $request->getUri()->getHost() === 'stage.makaira.io'
            ? 'gambio'
            : str_replace(['http://', 'https://', '.'], ['', '', '-'], $request->getUri()->getHost());

        try {
            MakairaInstallationService::callInstallationService($this->configurationService, $subdomain, $this->url->base());
        } catch (\Exception $exception) {
            $data['duration'] = 'Error';
            $template = $this->render($pageTitle, $templatePath, $data);
            return $response->write($template);
        }

        return $response->withRedirect('/admin/makaira/account');
    }
}
