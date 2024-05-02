<?php

namespace GXModules\Makaira\MakairaConnect;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Layout\Menu\Filter\FilterFactory;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantsStock;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Language\Services\LanguageService;
use GXModules\Makaira\MakairaConnect\App\Actions\Export;
use GXModules\Makaira\MakairaConnect\App\Actions\ReplaceAction;
use GXModules\Makaira\MakairaConnect\App\ChangesService;
use GXModules\Makaira\MakairaConnect\App\Documents\MakairaProduct;
use GXModules\Makaira\MakairaConnect\App\EventListener\VariantUpdateEventListener;
use GXModules\Makaira\MakairaConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\MakairaConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\MakairaConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\Makaira\MakairaConnect\App\GambioConnectService\GambioConnectPublicFieldsService;
use GXModules\Makaira\MakairaConnect\App\MakairaClient;
use GXModules\Makaira\MakairaConnect\App\MakairaLogger;
use GXModules\Makaira\MakairaConnect\App\Service\GambioConnectService;
use GXModules\Makaira\MakairaConnect\Admin\Actions\MakairaCheckoutAction;
use GXModules\Makaira\MakairaConnect\Admin\Actions\MakairaConnectAccount;
use GXModules\Makaira\MakairaConnect\Admin\Actions\MakairaConnectEntry;
use GXModules\Makaira\MakairaConnect\Admin\Actions\MakairaConnectManualSetup;
use GXModules\Makaira\MakairaConnect\Admin\Actions\MakairaConnectWelcome;
use GXModules\Makaira\MakairaConnect\Admin\Actions\StripeCheckoutCancelCallback;
use GXModules\Makaira\MakairaConnect\Admin\Actions\StripeCheckoutSuccessCallback;
use GXModules\Makaira\MakairaConnect\Admin\MenuFilter\IsInstalledFilter;
use GXModules\Makaira\MakairaConnect\Admin\MenuFilter\IsSetUpFilter;
use GXModules\Makaira\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\MakairaConnect\Admin\Services\ModuleStatusService;
use GXModules\Makaira\MakairaConnect\App\Core\MakairaRequest;

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
        $this->application->attachEventListener(UpdatedProductVariantsStock::class, VariantUpdateEventListener::class);
    }

    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            MakairaCheckoutAction::class,
            StripeCheckoutSuccessCallback::class,
            StripeCheckoutCancelCallback::class,
            MakairaConnectInstaller::class,
            MakairaConnectEntry::class,
            MakairaConnectManualSetup::class,
            MakairaConnectWelcome::class,
            MakairaConnectAccount::class,
            Export::class,
            VariantUpdateEventListener::class,
            LanguageService::class,
            MakairaRequest::class,
            ModuleConfigService::class,
            ModuleStatusService::class,
            IsSetUpFilter::class,
            IsInstalledFilter::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(\MakairaProductListingContentControl::class, ProductListingContentControl::class);

        $this->application->registerShared(MakairaCheckoutAction::class)
            ->addArgument($this->application);

        $this->application->registerShared(StripeCheckoutSuccessCallback::class)
            ->addArgument($this->application);

        $this->application->registerShared(StripeCheckoutCancelCallback::class)
            ->addArgument($this->application);

        $this->application->registerShared(MakairaConnectEntry::class)
            ->addArgument(ModuleStatusService::class);

        $this->application->registerShared(MakairaConnectManualSetup::class)
            ->addArgument(ModuleConfigService::class);

        $this->application->registerShared(MakairaConnectWelcome::class)
            ->addArgument(ModuleStatusService::class);
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

        $this->application->registerShared(VariantUpdateEventListener::class)
            ->addArgument(GambioConnectService::class);

        $this->application->registerShared(MakairaProduct::class)
            ->addArgument(ProductVariantsReadService::class);

        $this->application->registerShared(MakairaConnectInstaller::class)
            ->addArgument(Connection::class);

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
