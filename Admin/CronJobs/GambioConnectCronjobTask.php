<?php

use GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\GambioConnect\Admin\Services\StripeService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectPublicFieldsService;

class GambioConnectCronjobTask extends AbstractCronjobTask
{
    protected GambioConnectManufacturerService $gambioConnectManufacturerService;
    protected GambioConnectCategoryService $gambioConnectCategoryService;
    protected GambioConnectProductService $gambioConnectProductService;

    protected GambioConnectPublicFieldsService $gambioConnectPublicFieldsService;

    protected ModuleConfigService $moduleConfigService;


    public function getCallback($cronjobStartAsMicrotime): \Closure
    {
        $dependencies = $this->dependencies->getDependencies();

        $this->moduleConfigService = $dependencies['ModuleConfigService'];

        if ($this->moduleIsInstalledAndActive()) {
            $this->gambioConnectManufacturerService = new GambioConnectManufacturerService(
                $dependencies['MakairaClient'],
                $dependencies['LanguageReadService'],
                $dependencies['Connection'],
                $dependencies['MakairaLogger'],
                $dependencies['productVariantsRepository']
            );

            $this->gambioConnectCategoryService = new GambioConnectCategoryService(
                $dependencies['MakairaClient'],
                $dependencies['LanguageReadService'],
                $dependencies['Connection'],
                $dependencies['MakairaLogger'],
                $dependencies['productVariantsRepository']
            );

            $this->gambioConnectProductService = new GambioConnectProductService(
                $dependencies['MakairaClient'],
                $dependencies['LanguageReadService'],
                $dependencies['Connection'],
                $dependencies['MakairaLogger'],
                $dependencies['productVariantsRepository']
            );

            $this->gambioConnectPublicFieldsService = new GambioConnectPublicFieldsService(
                $dependencies['MakairaClient'],
                $dependencies['LanguageReadService'],
                $dependencies['Connection'],
                $dependencies['MakairaLogger'],
                $dependencies['productVariantsRepository']
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

                if(!$this->checkPublicFieldsSetup()) {
                    $this->logInfo('Makaira Public Fields Setup Has Started');

                    $this->gambioConnectPublicFieldsService->setUpPublicFields();

                    $this->logInfo('Makaira Public Fields has been setup');

                    $this->completePublicFieldsSetup();
                }
            };
        }
        return function () {
        };
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
        $makairaUrl = $this->moduleConfigService->getMakairaUrl();
        $makairaSecret = $this->moduleConfigService->getMakairaSecret();
        $makairaInstance = $this->moduleConfigService->getMakairaInstance();

        if (!$makairaUrl || !$makairaInstance || !$makairaSecret) {
            $this->logInfo('No Makaira Credentials found - CRON can not work');
            return false;
        }

        $stripeCheckoutId = $this->moduleConfigService->getStripeCheckoutId();
        $stripeOverride = $this->moduleConfigService->isStripeOverrideActive();

      if ($stripeCheckoutId) {
            $this->logInfo('Stripe Subscription ID found');
            $stripe = new StripeService();
            $checkoutSession = $stripe->getCheckoutSession($stripeCheckoutId);
            $isPaid = $checkoutSession->payment_status === "paid";
            if ($isPaid) {
                $this->logInfo("Stripe Subscription Status is Paid");
            }
            $installed = $this->moduleConfigService->getIsInstalled();
            
            if ($installed) {
                $this->logInfo('Module is Installed');
            }
            $active = $this->moduleConfigService->getIsActive();
            if ($active) {
                $this->logInfo('Module is Active');
            }
            return $installed && $active && $isPaid;
        }
        if ($stripeOverride) {
            $this->logInfo('Stripe Override is not active');
            $this->logInfo('No Stripe Subscription ID found');
            return true;
        }

        return false;
    }

    protected function checkPublicFieldsSetup(): bool
    {
        return $this->moduleConfigService->isPublicFieldsSetupDone();
    }

    private function completePublicFieldsSetup(): void
    {
        $this->moduleConfigService->setPublicFieldsSetupDone();
    }
}
