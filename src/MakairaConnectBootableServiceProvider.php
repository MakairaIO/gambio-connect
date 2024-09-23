<?php

namespace GXModules\MakairaIO\MakairaConnect;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Layout\Menu\Filter\FilterFactory;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Language\Services\LanguageService;
use GXModules\MakairaIO\MakairaConnect\Admin\Actions\MakairaConnectAccount;
use GXModules\MakairaIO\MakairaConnect\Admin\Actions\MakairaConnectEntry;
use GXModules\MakairaIO\MakairaConnect\Admin\Actions\MakairaConnectManualSetup;
use GXModules\MakairaIO\MakairaConnect\Admin\MenuFilter\IsInstalledFilter;
use GXModules\MakairaIO\MakairaConnect\Admin\MenuFilter\IsSetUpFilter;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleStatusService;
use GXModules\MakairaIO\MakairaConnect\App\Actions\Export;
use GXModules\MakairaIO\MakairaConnect\App\Actions\ReplaceAction;
use GXModules\MakairaIO\MakairaConnect\App\ChangesService;
use GXModules\MakairaIO\MakairaConnect\App\Core\MakairaRequest;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaProduct;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectPublicFieldsService;
use GXModules\MakairaIO\MakairaConnect\App\MakairaClient;
use GXModules\MakairaIO\MakairaConnect\App\MakairaLogger;
use GXModules\MakairaIO\MakairaConnect\App\Service\GambioConnectService;

class MakairaConnectBootableServiceProvider extends AbstractBootableServiceProvider
{
    public function boot(): void
    {
        $configurationService = $this->application->get(ConfigurationService::class);

        $moduleConfigService = new ModuleConfigService($configurationService);

        $moduleStatusService = new ModuleStatusService($moduleConfigService);
        $this->application->inflect(FilterFactory::class)->invokeMethod('addFilter', ['isInstalledFilter', new IsInstalledFilter(
            $moduleStatusService
        )]);
        $this->application->inflect(FilterFactory::class)->invokeMethod('addFilter', ['isSetUpFilter', new IsSetUpFilter(
            $moduleStatusService
        )]);
    }

    /**
     * {@inheritDoc}
     */
    public function provides(): array
    {
        return [
            MakairaConnectEntry::class,
            MakairaConnectManualSetup::class,
            MakairaConnectAccount::class,
            Export::class,
            LanguageService::class,
            MakairaRequest::class,
            ModuleConfigService::class,
            ModuleStatusService::class,
            IsSetUpFilter::class,
            IsInstalledFilter::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->application->registerShared(MakairaConnectEntry::class)
            ->addArgument(ModuleStatusService::class);

        $this->application->registerShared(MakairaConnectManualSetup::class)
            ->addArgument(ModuleConfigService::class);

        $this->application->registerShared(MakairaConnectAccount::class)
            ->addArgument(ModuleStatusService::class)
            ->addArgument(ModuleConfigService::class)
            ->addArgument(ChangesService::class);

        $this->application->registerShared(Export::class)
            ->addArgument(GambioConnectCategoryService::class)
            ->addArgument(GambioConnectProductService::class)
            ->addArgument(GambioConnectManufacturerService::class);

        $this->application->registerShared(ReplaceAction::class)
            ->addArgument(GambioConnectService::class);

        $this->application->registerShared(MakairaLogger::class);

        $this->application->registerShared(MakairaClient::class)
            ->addArgument(ConfigurationService::class);

        $this->application->registerShared(GambioConnectService::class)
            ->addArgument(MakairaClient::class)
            ->addArgument(LanguageReadService::class)
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class);

        $this->application->registerShared(GambioConnectProductService::class)
            ->addArgument(MakairaClient::class)
            ->addArgument(LanguageService::class)
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class)
            ->addArgument(ProductVariantsReadService::class);

        $this->application->registerShared(GambioConnectCategoryService::class)
            ->addArgument(MakairaClient::class)
            ->addArgument(LanguageService::class)
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class);

        $this->application->registerShared(GambioConnectManufacturerService::class)
            ->addArgument(MakairaClient::class)
            ->addArgument(LanguageService::class)
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class)
            ->addArgument(null);

        $this->application->registerShared(GambioConnectPublicFieldsService::class)
            ->addArgument(MakairaClient::class)
            ->addArgument(LanguageService::class)
            ->addArgument(Connection::class)
            ->addArgument(MakairaLogger::class);

        $this->application->registerShared(ChangesService::class)
            ->addArgument(Connection::class);

        $this->application->registerShared(MakairaProduct::class)
            ->addArgument(ProductVariantsReadService::class);

        $this->application->registerShared(ModuleConfigService::class)
            ->addArgument(ConfigurationService::class);

        $this->application->registerShared(ModuleStatusService::class)
            ->addArgument(ModuleConfigService::class);

        $this->application->registerShared(IsSetUpFilter::class)
            ->addArgument(ModuleStatusService::class);

        $this->application->registerShared(IsInstalledFilter::class)
            ->addArgument(ModuleStatusService::class);
    }
}
