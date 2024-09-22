<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectImporterConfigService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectPublicFieldsService;

class MakairaConnectCronjobTask extends AbstractCronjobTask
{
    protected GambioConnectPublicFieldsService $gambioConnectPublicFieldsService;

    protected GambioConnectImporterConfigService $gambioConnectImporterConfigService;

    protected ModuleConfigService $moduleConfigService;

    protected Connection $connection;


    public function getCallback($cronjobStartAsMicrotime): \Closure
    {
        $dependencies = $this->dependencies->getDependencies();

        $this->connection = $dependencies['Connection'];



        $this->moduleConfigService = $dependencies['ModuleConfigService'];

        if ($this->moduleIsInstalledAndActive()) {
            $gambioConnectService = new GambioConnectService(
                $dependencies['MakairaClient'],
                $dependencies['LanguageReadService'],
                $dependencies['Connection'],
                $dependencies['MakairaLogger'],
                $dependencies['productVariantsRepository']
            );

            $this->gambioConnectImporterConfigService = $gambioConnectService->getImporterConfigService();

            $this->gambioConnectPublicFieldsService = $gambioConnectService->getGambioConnectPublicFieldsService();

            return function () use($gambioConnectService) {
                if (!$this->checkImporterSetup()) {
                    $this->logInfo("Importer was not created yet - creating it now");
                    $this->gambioConnectImporterConfigService->setUpImporter();

                    $this->completeImporterSetUp();
                }

                $host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';

                $languages = $this->connection->createQueryBuilder()
                    ->select('code')
                    ->from('languages')
                    ->execute()
                    ->fetchAll(FetchMode::ASSOCIATIVE);

                $this->logInfo('MakairaConnect Cronjob Started');

                foreach($languages as $language) {
                    $this->logInfo('Begin Export for Language ' . $language['code']);
                    $client = new \GuzzleHttp\Client([
                        'base_uri' => $host . $language['code'],
                    ]);
                    try {
                        $client->get('shop.php?do=MakairaCronService/doExport&language='. $language['code']);
                    }catch(Exception $exception) {
                        $this->logInfo('Error in Export for Language ' . $language['code']);
                        $this->logError($exception->getMessage());
                    }
                    $this->logInfo('End Export for Language ' . $language['code']);
                }

                $gambioConnectService->exportIsDoneForType('product');

                $gambioConnectService->exportIsDoneForType('manufacturer');

                $gambioConnectService->exportIsDoneForType('category');

                if (!$this->checkPublicFieldsSetup()) {
                    try {
                        $this->logInfo('Makaira Public Fields Setup Has Started');

                        $this->gambioConnectPublicFieldsService->setUpProductPublicFields();

                        $this->gambioConnectPublicFieldsService->setUpCategoryPublicFields();

                        $this->logInfo('Makaira Public Fields has been setup');
                    }catch(Exception) {
                        return;
                    }

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
        $importers = $this->gambioConnectImporterConfigService->checkImporter();

        if(count($importers) > 0 && !$this->moduleConfigService->isMakairaImporterSetupDone()) {
            $this->moduleConfigService->setMakairaImporterSetupDone();
        }

        return $this->moduleConfigService->isMakairaImporterSetupDone();
    }

    public function completeImporterSetup(): void
    {
        $this->moduleConfigService->setMakairaImporterSetupDone();
    }
}
