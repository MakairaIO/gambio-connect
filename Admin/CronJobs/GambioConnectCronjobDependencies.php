<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class GambioConnectCronjobDependencies extends AbstractCronjobDependencies
{
    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        $configurationFinder = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationFinder::class);
        $configurationService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationService::class);
        $makairaClient = new \GXModules\Makaira\GambioConnect\App\MakairaClient($configurationService);
        $connection = LegacyDependencyContainer::getInstance()->get(\Doctrine\DBAL\Connection::class);
        $languageReadService = LegacyDependencyContainer::getInstance()->get(\Gambio\Admin\Modules\Language\Services\LanguageReadService::class);
        $makairaLogger = new \GXModules\Makaira\GambioConnect\App\MakairaLogger();
        $productVariantsReadService = new \Gambio\Admin\Modules\ProductVariant\App\ProductVariantsRepository(
            new \Gambio\Admin\Modules\ProductVariant\App\Data\ProductVariantsReader($connection),
            new \Gambio\Admin\Modules\ProductVariant\App\Data\ProductVariantsDeleter($connection),
            new \Gambio\Admin\Modules\ProductVariant\App\Data\ProductVariantsInserter($connection),
            new \Gambio\Admin\Modules\ProductVariant\App\Data\ProductVariantsUpdater($connection),
            new \Gambio\Admin\Modules\ProductVariant\App\Data\ProductVariantsMapper(
                new \Gambio\Admin\Modules\ProductVariant\Services\ProductVariantFactory(),
            ),
            LegacyDependencyContainer::getInstance()->get(\Psr\EventDispatcher\EventDispatcherInterface::class)
        );
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
            'productVariantsRepository' => $productVariantsReadService,
            'active' => $this->storage->get('GambioConnect', 'active')
        ];
    }
}
