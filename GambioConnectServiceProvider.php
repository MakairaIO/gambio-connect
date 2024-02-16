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
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Language\Services\LanguageService;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectAccount;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectDocument;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectEntry;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectFAQ;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectManualSetup;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectWelcome;
use GXModules\Makaira\GambioConnect\Admin\Actions\MakairaCheckoutAction;
use GXModules\Makaira\GambioConnect\Admin\Actions\StripeCheckoutCancelCallback;
use GXModules\Makaira\GambioConnect\Admin\Actions\StripeCheckoutSuccessCallback;
use GXModules\Makaira\GambioConnect\Admin\CronJobs\GambioConnectCronjobDependencies;
use GXModules\Makaira\GambioConnect\Admin\CronJobs\GambioConnectCronjobLogger;
use GXModules\Makaira\GambioConnect\Admin\CronJobs\GambioConnectCronjobTask;
use GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\GambioConnect\Admin\Services\ModuleStatusService;
use GXModules\Makaira\GambioConnect\App\Actions\Export;
use GXModules\Makaira\GambioConnect\App\Actions\ReplaceAction;
use GXModules\Makaira\GambioConnect\App\ChangesService;
use GXModules\Makaira\GambioConnect\App\Core\MakairaRequest;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaProduct;
use GXModules\Makaira\GambioConnect\App\EventListeners\VariantUpdateEventListener;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectPublicFieldsService;
use GXModules\Makaira\GambioConnect\App\MakairaClient;
use GXModules\Makaira\GambioConnect\App\MakairaLogger;
use GXModules\Makaira\GambioConnect\App\Service\GambioConnectService;

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
            MakairaCheckoutAction::class,
            StripeCheckoutSuccessCallback::class,
            StripeCheckoutCancelCallback::class,
            GambioConnectInstaller::class,
            GambioConnectEntry::class,
            GambioConnectManualSetup::class,
            GambioConnectDocument::class,
            GambioConnectWelcome::class,
            GambioConnectAccount::class,
            GambioConnectFAQ::class,
            Export::class,
            VariantUpdateEventListener::class,
            LanguageService::class,
            MakairaRequest::class,
            ModuleConfigService::class,
            ModuleStatusService::class
        ];
    }


    /**
     * @inheritcDoc
     */
    public function register(): void
    {
        $this->application->registerShared(MakairaCheckoutAction::class)
            ->addArgument($this->application);

        $this->application->registerShared(StripeCheckoutSuccessCallback::class)
            ->addArgument($this->application);

        $this->application->registerShared(StripeCheckoutCancelCallback::class)
            ->addArgument($this->application);

        $this->application->registerShared(GambioConnectEntry::class)
            ->addArgument(ModuleStatusService::class);

        $this->application->registerShared(GambioConnectManualSetup::class)
            ->addArgument(ModuleConfigService::class);

        $this->application->registerShared(GambioConnectDocument::class);
        $this->application->registerShared(GambioConnectWelcome::class)
            ->addArgument(ModuleStatusService::class);
        $this->application->registerShared(GambioConnectAccount::class)
            ->addArgument(ModuleStatusService::class);
        $this->application->registerShared(GambioConnectFAQ::class);


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

        $this->application->registerShared(GambioConnectPublicFieldsService::class)
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

        $this->application->registerShared(ModuleConfigService::class)
            ->addArgument(ConfigurationService::class);

        $this->application->registerShared(ModuleStatusService::class)
            ->addArgument(ModuleConfigService::class);
    }

    public function boot(): void
    {
        $this->application->attachEventListener(UpdatedProductVariantsStock::class, VariantUpdateEventListener::class);
    }
}
