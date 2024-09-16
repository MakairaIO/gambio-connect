<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectImporterConfigService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectPublicFieldsService;
use GXModules\MakairaIO\MakairaConnect\App\MakairaClient;

class MakairaConnectCronjobTask extends AbstractCronjobTask
{
    protected GambioConnectManufacturerService $gambioConnectManufacturerService;
    protected GambioConnectCategoryService $gambioConnectCategoryService;
    protected GambioConnectProductService $gambioConnectProductService;

    protected GambioConnectPublicFieldsService $gambioConnectPublicFieldsService;

    protected GambioConnectImporterConfigService $gambioConnectImporterConfigService;

    protected ModuleConfigService $moduleConfigService;

    protected MakairaClient $makairaClient;


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

                $this->logInfo('MakairaConnect Cronjob Started');

                $this->logInfo('Begin Export Manufacturers to PersistenceLayer');

                $this->gambioConnectManufacturerService->export();

                $this->logInfo('Begin Export Categories to PersistenceLayer');

                $this->gambioConnectCategoryService->export();

                $this->logInfo('Begin Export Products to PersistenceLayer');

                $this->gambioConnectProductService->export();

                $this->logInfo('All Exports to PersistenceLayer Successful');

                if (!$this->checkPublicFieldsSetup()) {
                    $this->logInfo('Makaira Public Fields Setup Has Started');

                    $this->gambioConnectPublicFieldsService->setUpProductPublicFields();

                    $this->gambioConnectPublicFieldsService->setUpCategoryPublicFields();

                    $this->logInfo('Makaira Public Fields has been setup');

                    $this->completePublicFieldsSetup();
                }

                $this->updateBookedFeatures();
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

        return true;
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

    private function updateBookedFeatures(): void
    {
        $features = $this->makairaClient->getFeatures();

        $this->moduleConfigService->setMakairaSearchBooked(in_array('search', $features));

        $this->moduleConfigService->setMakairaPromotionBooked(in_array('promotion', $features));

        $this->moduleConfigService->setMakairaRecommendationsBooked(in_array('recommendations', $features));
    }
}
