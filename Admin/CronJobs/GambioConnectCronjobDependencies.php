<?php

use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;

class GambioConnectCronjobDependencies extends AbstractCronjobDependencies
{
    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        $configurationFinder = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationFinder::class);
        $makairaClient = new \GXModules\Makaira\GambioConnect\App\MakairaClient($configurationFinder);
        $connection = LegacyDependencyContainer::getInstance()->get(\Doctrine\DBAL\Connection::class);
        $languageReadService = LegacyDependencyContainer::getInstance()->get(\Gambio\Admin\Modules\Language\Services\LanguageReadService::class);
        $makairaLogger = new \GXModules\Makaira\GambioConnect\App\MakairaLogger();
        return [
            'MakairaClient' => $makairaClient,
            'LanguageReadService' => $languageReadService,
            'Connection' => $connection,
            'MakairaLogger' => $makairaLogger,
            'ConfigurationFinder' => $configurationFinder,
            'active' => $this->storage->get('GambioConnect', 'active')
        ];
    }
}
