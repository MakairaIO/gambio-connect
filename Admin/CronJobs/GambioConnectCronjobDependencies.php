<?php

use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository;
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
        $configurationService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationService::class);
        $makairaClient = new \GXModules\Makaira\GambioConnect\App\MakairaClient($configurationFinder);
        $connection = LegacyDependencyContainer::getInstance()->get(\Doctrine\DBAL\Connection::class);
        $languageReadService = LegacyDependencyContainer::getInstance()->get(\Gambio\Admin\Modules\Language\Services\LanguageReadService::class);
        $makairaLogger = new \GXModules\Makaira\GambioConnect\App\MakairaLogger();
        $productVariantsRepository = LegacyDependencyContainer::getInstance()->get(ProductVariantsRepository::class);
        $moduleConfigService = new \GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService(
            $configurationService
        );
        return [
            'MakairaClient' => $makairaClient,
            'LanguageReadService' => $languageReadService,
            'Connection' => $connection,
            'MakairaLogger' => $makairaLogger,
            'ConfigurationFinder' => $configurationFinder,
            'ConfigurationService' => $configurationService,
            'ModuleConfigService' => $moduleConfigService,
            'productVariantsRepository' => $productVariantsRepository,
            'active' => $this->storage->get('GambioConnect', 'active')
        ];
    }
}