<?php

namespace GXModules\Makaira\MakairaConnect\Admin\Actions;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Makaira\MakairaConnect\Admin\Services\StripeService;

class MakairaCheckoutAction extends AbstractAction
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
        $stripeService = new StripeService();

        $stripeService->setConfigurationService($this->configurationService);

        if ($request->getParsedBodyParam(StripeService::BUNDLE_PRICE_ID) === "on") {
            $stripeService->addPriceId(StripeService::BUNDLE_PRICE_ID);
        } else {
            if ($request->getParsedBodyParam(StripeService::SEARCH_PRICE_ID) === "on") {
                $stripeService->addPriceId(StripeService::SEARCH_PRICE_ID);
            }
            if ($request->getParsedBodyParam(StripeService::ADS_PRICE_ID) === "on") {
                $stripeService->addPriceId(StripeService::ADS_PRICE_ID);
                if ($request->getParsedBodyParam(StripeService::SEARCH_PRICE_ID) !== "on") {
                    $stripeService->addPriceId(StripeService::SEARCH_PRICE_ID);
                }
            }

            if ($request->getParsedBodyParam(StripeService::RECOMMENDATIONS_PRICE_ID) === "on") {
                $stripeService->addPriceId(StripeService::RECOMMENDATIONS_PRICE_ID);
            }
        }

        $successUrl = $this->url->admin() . '/makaira/gambio-connect/stripe-checkout-success-callback';

        $cancelUrl = $this->url->admin() . '/makaira/welcome';

        $stripeService->setSuccessUrl($successUrl);

        $stripeService->setCancelUrl($cancelUrl);

        $session = $stripeService->createCheckoutSession();

        return $response->withRedirect($session->url);
    }
}
