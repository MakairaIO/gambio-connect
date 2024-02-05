<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantsStock;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository;
use Gambio\Core\Application\DependencyInjection\AbstractModuleServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use GXModules\Makaira\GambioConnect\Admin\CronJobs\GambioConnectCronjobDependencies;
use GXModules\Makaira\GambioConnect\Admin\CronJobs\GambioConnectCronjobLogger;
use GXModules\Makaira\GambioConnect\Admin\CronJobs\GambioConnectCronjobTask;
use GXModules\Makaira\GambioConnect\App\Actions\Export;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectAccount;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectDocument;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectFAQ;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectOverview;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectWelcome;
use GXModules\Makaira\GambioConnect\App\ChangesService;
use GXModules\Makaira\GambioConnect\App\Core\MakairaRequest;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaProduct;
use GXModules\Makaira\GambioConnect\App\EventListeners\VariantUpdateEventListener;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\Makaira\GambioConnect\App\MakairaClient;
use GXModules\Makaira\GambioConnect\App\MakairaLogger;
use GXModules\Makaira\GambioConnect\App\Utils\ModuleConfig;
use GXModules\Makaira\GambioConnect\Service\GambioConnectService;
use Gambio\Core\Language\Services\LanguageService;

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
            GambioConnectDocument::class,
            GambioConnectWelcome::class,
            GambioConnectAccount::class,
            GambioConnectFAQ::class,
            Export::class,
            VariantUpdateEventListener::class,
            LanguageService::class,
            MakairaRequest::class,
            ModuleConfig::class
        ];
    }


    /**
     * @inheritcDoc
     */
    public function register(): void
    {
        $this->application->registerShared(GambioConnectOverview::class);
        $this->application->registerShared(GambioConnectDocument::class);
        $this->application->registerShared(GambioConnectWelcome::class);
        $this->application->registerShared(GambioConnectAccount::class);
        $this->application->registerShared(GambioConnectFAQ::class);


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
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class)
            ->addArgument(ProductVariantsRepository::class);

        $this->application->registerShared(GambioConnectCategoryService::class)
            ->addArgument(MakairaClient::class)
            ->addArgument(LanguageReadService::class)
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class)
            ->addArgument(ProductVariantsRepository::class);

        $this->application->registerShared(GambioConnectManufacturerService::class)
            ->addArgument(MakairaClient::class)
            ->addArgument(LanguageReadService::class)
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class)
            ->addArgument(ProductVariantsRepository::class);

        $this->application->registerShared(ChangesService::class)
            ->addArgument(Connection::class);

        $this->application->registerShared(VariantUpdateEventListener::class)
            ->addArgument(GambioConnectService::class);

        $this->application->registerShared(MakairaProduct::class)
            ->addArgument(ProductVariantsReadService::class);

        $this->application->registerShared(GambioConnectInstaller::class)
            ->addArgument(Connection::class);

        $this->application->registerShared(ModuleConfig::class)
            ->addArgument(ConfigurationFinder::class);
    }

    public function boot(): void
    {
        $this->application->attachEventListener(UpdatedProductVariantsStock::class, VariantUpdateEventListener::class);
    }
}
