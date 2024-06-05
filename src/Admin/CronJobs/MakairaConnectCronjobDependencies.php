<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class MakairaConnectCronjobDependencies extends AbstractCronjobDependencies
{
    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        $configurationService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationService::class);
        $makairaClient = new \GXModules\Makaira\MakairaConnect\App\MakairaClient($configurationService);
        $connection = LegacyDependencyContainer::getInstance()->get(\Doctrine\DBAL\Connection::class);
        $languageReadService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Language\Services\LanguageService::class);
        $makairaLogger = new \GXModules\Makaira\MakairaConnect\App\MakairaLogger();
        $productVariantsReadService = new \Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsRepository(
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsReader($connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsDeleter($connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsInserter($connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsUpdater($connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsMapper(
                new \Gambio\Admin\Modules\ProductVariant\Services\ProductVariantFactory(),
            ),
            LegacyDependencyContainer::getInstance()->get(\Psr\EventDispatcher\EventDispatcherInterface::class)
        );
        $moduleConfigService = new \GXModules\Makaira\MakairaConnect\Admin\Services\ModuleConfigService(
            $configurationService
        );
        return [
            'MakairaClient' => $makairaClient,
            'LanguageReadService' => $languageReadService,
            'Connection' => $connection,
            'MakairaLogger' => $makairaLogger,
            'ModuleConfigService' => $moduleConfigService,
            'productVariantsRepository' => $productVariantsReadService,
            'active' => $this->storage->get('MakairaGambioConnect', 'active')
        ];
    }
}
