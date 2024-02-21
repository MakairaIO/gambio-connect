<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use GXModules\Makaira\GambioConnect\Admin\Services\MakairaInstallationService;
use GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\GambioConnect\Admin\Services\StripeService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectImporterConfigService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectPublicFieldsService;

class GambioConnectCronjobTask extends AbstractCronjobTask
{
    protected GambioConnectManufacturerService $gambioConnectManufacturerService;
    protected GambioConnectCategoryService $gambioConnectCategoryService;
    protected GambioConnectProductService $gambioConnectProductService;

    protected GambioConnectPublicFieldsService $gambioConnectPublicFieldsService;

    protected GambioConnectImporterConfigService $gambioConnectImporterConfigService;

    protected ModuleConfigService $moduleConfigService;


    public function getCallback($cronjobStartAsMicrotime): \Closure
    {
        $dependencies = $this->dependencies->getDependencies();

        $this->moduleConfigService = $dependencies['ModuleConfigService'];

        if ($this->moduleIsInstalledAndActive()) {
            $gambioConnectService = new GambioConnectService(
                $dependencies['MakairaClient'],
                $dependencies['LanguageReadService'],
                $dependencies['Connection'],
                $dependencies['MakairaLogger'],
                $dependencies['productVariantsRepository']
            );

            $this->gambioConnectProductService = $gambioConnectService->getProductService();

            $this->gambioConnectCategoryService = $gambioConnectService->getCategoryService();

            $this->gambioConnectManufacturerService = $gambioConnectService->getManufacturerService();

            $this->gambioConnectImporterConfigService = $gambioConnectService->getImporterConfigService();

            $this->gambioConnectPublicFieldsService = $gambioConnectService->getGambioConnectPublicFieldsService();

            return function () {
                if (!$this->checkImporterSetup()) {
                    $this->logInfo("Importer was not created yet - creating it now");
                    $this->gambioConnectImporterConfigService->setUpImporter();

                    $this->completeImporterSetUp();
                }

                $this->logInfo('GambioConnect Cronjob Started');

                $this->logInfo('Begin Export Manufacturers to PersistenceLayer');

                $this->gambioConnectManufacturerService->export();

                $this->logInfo('Begin Export Categories to PersistenceLayer');

                $this->gambioConnectCategoryService->export();

                $this->logInfo('Begin Export Products to PersistenceLayer');

                $this->gambioConnectProductService->export();

                $this->logInfo('All Exports to PersistenceLayer Successful');

                if (!$this->checkPublicFieldsSetup()) {
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

        if (!$this->moduleConfigService->isMakairaInstallationServiceCalled()) {
            $this->logError("Makaira Installation Service has not been called yet");
            MakairaInstallationService::callInstallationService($this->moduleConfigService);
            return false;
        }

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
            $this->logInfo("Stripe Checkout Session Payment Status: " . $checkoutSession->payment_status);
            $isPaid = $checkoutSession->payment_status === "paid";
            if ($isPaid) {
                $this->logInfo("Stripe Subscription Status is Paid");
            }
            $installed = $this->moduleConfigService->getIsInstalled();

            if ($installed) {
                $this->logInfo('Module is Installed');
            } else {
                $this->logInfo('Module is not Installed');
            }
            return $installed && $isPaid;
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

    protected function checkImporterSetup(): bool
    {
        return $this->moduleConfigService->isMakairaImporterSetupDone();
    }

    public function completeImporterSetup(): void
    {
        $this->moduleConfigService->setMakairaImporterSetupDone();
    }
}
