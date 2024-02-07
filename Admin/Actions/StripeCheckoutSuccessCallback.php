<?php

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Makaira\GambioConnect\Admin\Services\MakairaInstallationService;
use GXModules\Makaira\GambioConnect\Admin\Services\StripeService;

class StripeCheckoutSuccessCallback extends AdminModuleAction
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
        $pageTitle = 'Makaira Gambio Connect - Successful Checkout';
        $templatePath = __DIR__ . '/../../ui/template/stripe/success.html';

        $stripe = new StripeService();
        
        $checkoutSessionId = $this->configurationService->find('modules/MakairaGambioConnect/stripeCheckoutSession')?->value();
        $checkoutSession = $stripe->getCheckoutSession($checkoutSessionId);
        
        $email = $checkoutSession->customer_details->email;
        
        $this->configurationService->save('modules/MakairaGambioConnect/stripeCheckoutEmail', $email);
        
        $installationService = new MakairaInstallationService();
        $installationService->setEmail($email);
        $installationService->setCheckoutSessionId($checkoutSessionId);
        $installationService->setShopUrl($request->getUri()->getHost());
        $installationService->setSubdomain(str_replace(['http://', 'https://', '.'], ['', '','-'], $this->url->base()));
        $installationService->setCallbackUri($this->url->base() .'/shop.php?do=MakairaInstallationService');
        $installationServiceResponse = $installationService->callRegistrationService();

        $responseData = json_decode($installationServiceResponse->getBody()->getContents());

        $data = [
            'duration' => $responseData->estimate_duration_time
        ];

        $template = $this->render($pageTitle, $templatePath, $data);

        return $response->write($template);
    }
}