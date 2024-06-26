<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsDeleter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsInserter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsMapper;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsReader;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsUpdater;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsRepository;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;

class GambioConnectCronjobDependencies extends AbstractCronjobDependencies
{
    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        $configurationService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationService::class);
        $makairaClient = new \GXModules\Makaira\GambioConnect\App\MakairaClient($configurationService);
        $connection = LegacyDependencyContainer::getInstance()->get(\Doctrine\DBAL\Connection::class);
        $languageReadService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Language\Services\LanguageService::class);
        $makairaLogger = new \GXModules\Makaira\GambioConnect\App\MakairaLogger();

        $productVariantsReadService = new ProductVariantsRepository(
            new ProductVariantsReader($connection),
            new ProductVariantsDeleter($connection),
            new ProductVariantsInserter($connection),
            new ProductVariantsUpdater($connection),
            new ProductVariantsMapper(
                new ProductVariantFactory(),
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
            'ModuleConfigService' => $moduleConfigService,
            'productVariantsRepository' => $productVariantsReadService,
            'active' => $this->storage->get('GambioConnect', 'active')
        ];
    }
}
