<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectImporterConfigService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectPublicFieldsService;

class MakairaConnectCronjobTask extends AbstractCronjobTask
{
    protected GambioConnectPublicFieldsService $gambioConnectPublicFieldsService;

    protected GambioConnectImporterConfigService $gambioConnectImporterConfigService;

    protected ModuleConfigService $moduleConfigService;

    protected Connection $connection;

    private const CHANGE_TYPES = [
        'manufacturer',
        'category',
        'product'
    ];

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

            return function () use ($gambioConnectService): void {
                if (! $this->checkImporterSetup()) {
                    $this->logInfo('Importer was not created yet - creating it now');
                    $this->gambioConnectImporterConfigService->setUpImporter();

                    $this->completeImporterSetUp();
                }

                $errors = false;

                $host = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/';

                $languages = $this->connection->createQueryBuilder()
                    ->select('code')
                    ->from('languages')
                    ->execute()
                    ->fetchAll(FetchMode::ASSOCIATIVE);

                $limit = 500;

                $query = $this->connection->createQueryBuilder()
                    ->select('gambio_id')
                    ->from(\GXModules\MakairaIO\MakairaConnect\App\ChangesService::TABLE_NAME)
                    ->setMaxResults(500);

                $changes = [];

                foreach(self::CHANGE_TYPES as $changeType) {
                    $changes[$changeType] = $query->where('type = :type')->setParameter('type', $changeType)->execute()->fetchAll(FetchMode::ASSOCIATIVE);
                }

                $this->logInfo('MakairaConnect Cronjob Started');

                $this->logInfo('Check count of Changes for Makaira');

                $deleteIds = [];

                foreach(self::CHANGE_TYPES as $changeType) {
                    foreach($languages as $language) {
                        $this->logInfo('Begin Export of '.$limit.' Datasets for Language ' . $language['code']);
                        $client = new \GuzzleHttp\Client([
                            'base_uri' => $host . $language['code'],
                        ]);
                        try {
                            $client->post(
                                'shop.php?do=MakairaCronService/doExport&language=' . $language['code'],
                                [
                                    'json' => [
                                        'type' => $changeType,
                                        'changes' => $changes[$changeType],
                                    ]
                                ]
                            );
                            $deleteIds = array_merge($deleteIds, $changes[$changeType]);
                        } catch (Exception $exception) {
                            $this->logInfo('Error in Export for Language ' . $language['code']);
                            $this->logError($exception->getMessage());
                        }
                        $this->logInfo('End Export of '.$limit.' Datasets for Language ' . $language['code']);
                    }
                }

                $this->connection->createQueryBuilder()
                    ->delete(\GXModules\MakairaIO\MakairaConnect\App\ChangesService::TABLE_NAME)
                    ->add('where', $this->connection->createQueryBuilder()->expr()->in('id', $changes))
                ->execute();

                if (! $this->checkPublicFieldsSetup()) {
                    try {
                        $this->logInfo('Makaira Public Fields Setup Has Started');

                        $this->gambioConnectPublicFieldsService->setUpProductPublicFields();

                        $this->gambioConnectPublicFieldsService->setUpCategoryPublicFields();

                        $this->logInfo('Makaira Public Fields has been setup');
                    } catch (Exception) {
                        return;
                    }

                    $this->completePublicFieldsSetup();
                }
            };
        }

        return function (): void {};
    }

    protected function logInfo(string $message): void
    {
        $this->logger->log(['message' => $message, 'level' => 'info']);
    }

    protected function logError(string $message): void
    {
        $this->logger->logError(['message' => $message, 'level' => 'error']);
    }

    protected function logNotice(string $message): void
    {
        $this->logger->log(['message' => $message, 'level' => 'notice']);
    }

    protected function moduleIsInstalledAndActive(): bool
    {
        $makairaUrl = $this->moduleConfigService->getMakairaUrl();
        $makairaSecret = $this->moduleConfigService->getMakairaSecret();
        $makairaInstance = $this->moduleConfigService->getMakairaInstance();
        $cronStatus = $this->moduleConfigService->getCronjobStatus();

        if (! $makairaUrl || ! $makairaInstance || ! $makairaSecret) {
            $this->logInfo('No Makaira Credentials found - CRON can not work');

            return false;
        }

        if(!$cronStatus) {
            $this->logInfo('CRON Job Inactive');
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

        if (count($importers) > 0 && ! $this->moduleConfigService->isMakairaImporterSetupDone()) {
            $this->moduleConfigService->setMakairaImporterSetupDone();
        }

        return $this->moduleConfigService->isMakairaImporterSetupDone();
    }

    public function completeImporterSetup(): void
    {
        $this->moduleConfigService->setMakairaImporterSetupDone();
    }
}
