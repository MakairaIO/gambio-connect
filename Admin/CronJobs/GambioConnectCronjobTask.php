<?php

use GXModules\Makaira\GambioConnect\Admin\Services\StripeService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;

class GambioConnectCronjobTask extends AbstractCronjobTask
{
    protected GambioConnectManufacturerService $gambioConnectManufacturerService;
    protected GambioConnectCategoryService     $gambioConnectCategoryService;
    protected GambioConnectProductService      $gambioConnectProductService;


    public function getCallback($cronjobStartAsMicrotime): \Closure
    {
        $dependencies = $this->dependencies->getDependencies();

        if ($this->moduleIsInstalledAndActive()) {
            $this->gambioConnectManufacturerService = new GambioConnectManufacturerService(
                $dependencies['MakairaClient'],
                $dependencies['LanguageReadService'],
                $dependencies['Connection'],
                $dependencies['MakairaLogger']
            );

            $this->gambioConnectCategoryService = new GambioConnectCategoryService(
                $dependencies['MakairaClient'],
                $dependencies['LanguageReadService'],
                $dependencies['Connection'],
                $dependencies['MakairaLogger']
            );

            $this->gambioConnectProductService = new GambioConnectProductService(
                $dependencies['MakairaClient'],
                $dependencies['LanguageReadService'],
                $dependencies['Connection'],
                $dependencies['MakairaLogger']
            );

            return function () {
                $this->logInfo('GambioConnect Cronjob Started');

                $this->logInfo('Begin Export Manufacturers to PersistenceLayer');

                $this->gambioConnectManufacturerService->export();

                $this->logInfo('Begin Export Categories to PersistenceLayer');

                $this->gambioConnectCategoryService->export();

                $this->logInfo('Begin Export Products to PersistenceLayer');

                $this->gambioConnectProductService->export();

                $this->logInfo('All Exports to PersistenceLayer Successful');
            };
        }
    }


    /**
     * @param string $message
     *
     * @return void
     */
    protected function logInfo(string $message): void
    {
        $this->logger->log(['message' => $message, 'level' => 'info']);
    }


    /**
     * @param string $message
     *
     * @return void
     */
    protected function logError(string $message): void
    {
        $this->logger->logError(['message' => $message, 'level' => 'error']);
    }


    /**
     * @param string $message
     *
     * @return void
     */
    protected function logNotice(string $message): void
    {
        $this->logger->log(['message' => $message, 'level' => 'notice']);
    }

    protected function moduleIsInstalledAndActive(): bool
    {
        $configurationFinder = $this->dependencies->getDependencies()['ConfigurationFinder'];

        $makairaUrl = $configurationFinder->get('modules/MakairaGambioConnect/makairaUrl')->value();

        $makairaSecret = $configurationFinder->get('modules/MakairaGambioConnect/makairaSecret')->value();

        $makairaInstance = $configurationFinder->get('modules/MakairaGambioConnect/makairaInstance')->value();

        if (!$makairaUrl || !$makairaInstance || !$makairaSecret) {
            $this->logInfo('No Makaira Credentials found - CRON can not work');
            return false;
        }

        $stripeCheckoutId = $configurationFinder->get('modules/MakairaGambioConnect/stripeCheckoutSession')?->value();
        $stripeOverride = $configurationFinder->get('modules/MakairaGambioConnect/stripeOverride')?->value();
        if (!$stripeOverride) {
            $this->logInfo('Stripe Override is not active');
            if ($stripeCheckoutId) {
                $this->logInfo('Stripe Subscription ID found');
                $stripe = new StripeService();
                $checkoutSession = $stripe->getCheckoutSession($stripeCheckoutId);
                $isPaid = $checkoutSession->payment_status === "paid";
                if ($isPaid) {
                    $this->logInfo("Stripe Subscription Status is Paid");
                }
                $installed = (bool)$configurationFinder->get('gm_configuration/MODULE_CENTER_MAKAIRAGAMBIOCONNECT_INSTALLED');
                if ($installed) {
                    $this->logInfo('Module is Installed');
                }
                $active = (bool)$configurationFinder->get('modules/MakairaGambioConnect/active');
                if ($active) {
                    $this->logInfo('Module is Active');
                }
                return $installed && $active && $isPaid;
            }
            $this->logInfo('No Stripe Subscription ID found');
        } else if ($stripeOverride && $stripeCheckoutId) {
            $this->logInfo('Stripe Override is active but Stripe Checkout Session ID if found');
            return false;
        }

        return false;
    }
}
