<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect;

use Gambio\Admin\Modules\Configuration\App\Data\Repositories\CategoryRepository;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Core\Application\DependencyInjection\AbstractModuleServiceProvider;
use GXModules\Makaira\GambioConnect\App\Actions\Export;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectOverview;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\Makaira\GambioConnect\App\MakairaClient;
use GXModules\Makaira\GambioConnect\App\MakairaLogger;
use GXModules\Makaira\GambioConnect\Service\GambioConnectService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService;
use Doctrine\DBAL\Connection;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantsStock;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use GXModules\Makaira\GambioConnect\App\ChangesService;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaProduct;
use GXModules\Makaira\GambioConnect\App\EventListeners\VariantUpdateEventListener;


/**
 * Class GambioConnectServiceProvider
 *
 * @package GXModules\Makaira\GambioConnect
 */
class GambioConnectServiceProvider extends AbstractModuleServiceProvider
{
    /**
     * @inheritcDoc
     */
    public function provides(): array
    {
        return [
            GambioConnectInstaller::class,
            GambioConnectOverview::class,
            Export::class,
            VariantUpdateEventListener::class,
        ];
    }


    /**
     * @inheritcDoc
     */
    public function register(): void
    {
        $this->application->registerShared(GambioConnectOverview::class);
        $this->application->registerShared(Export::class)
            ->addArgument(GambioConnectCategoryService::class)
            ->addArgument(GambioConnectProductService::class)
            ->addArgument(GambioConnectManufacturerService::class);

        $this->application->registerShared(MakairaLogger::class);
        $this->application->registerShared(MakairaClient::class)
            ->addArgument(ConfigurationFinder::class);

        $this->application->registerShared(GambioConnectProductService::class)
            ->addArgument(MakairaClient::class)
            ->addArgument(LanguageReadService::class)
            ->addArgument(ProductVariantsReadService::class)
            ->addArgument(AdditionalOptionReadService::class)
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class);
        
        $this->application->registerShared(GambioConnectCategoryService::class)
            ->addArgument(MakairaClient::class)
            ->addArgument(LanguageReadService::class)
            ->addArgument(ProductVariantsReadService::class)
            ->addArgument(AdditionalOptionReadService::class)
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class);
        
        $this->application->registerShared(GambioConnectManufacturerService::class)
            ->addArgument(MakairaClient::class)
            ->addArgument(LanguageReadService::class)
            ->addArgument(ProductVariantsReadService::class)
            ->addArgument(AdditionalOptionReadService::class)
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class)
            ->addArgument(ChangesService::class);

        $this->application->registerShared(ChangesService::class)
            ->addArgument(Connection::class);

        $this->application->registerShared(VariantUpdateEventListener::class)
            ->addArgument(GambioConnectService::class);

        $this->application->registerShared(MakairaProduct::class)
            ->addArgument(ProductVariantsReadService::class);
        
        $this->application->registerShared(GambioConnectInstaller::class)
            ->addArgument(Connection::class);
    }

    public function boot(): void
    {
        $this->application->attachEventListener(UpdatedProductVariantsStock::class, VariantUpdateEventListener::class);
    }
}
