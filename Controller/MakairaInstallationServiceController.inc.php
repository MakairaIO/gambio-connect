<?php

use ContentViewInterface;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;
use HttpContextReaderInterface;
use HttpResponseProcessorInterface;
use HttpViewController;

/**
 *
 * @link http://shop-url.de/shop.php?do=MakairaInstallationService
 */
class MakairaInstallationServiceController extends HttpViewController
{
    private ConfigurationService $configurationService;

    private ModuleConfigService $moduleConfigService;

    public function __construct(
        HttpContextReaderInterface $httpContextReader,
        HttpResponseProcessorInterface $httpResponseProcessor,
        ContentViewInterface $defaultContentView
    ) {
        parent::__construct($httpContextReader, $httpResponseProcessor, $defaultContentView);

        $this->configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);

        $this->moduleConfigService = new ModuleConfigService($this->configurationService);
    }

    public function actionDefault(): \JsonHttpControllerResponse
    {
        if ($this->moduleConfigService->getStripeCheckoutId() === $this->_getPostData('stripeCheckoutId')) {
            $this->moduleConfigService->setMakairaUrl($this->_getPostData('url'));

            $this->moduleConfigService->setMakairaInstance($this->_getPostData('instance'));

            $this->moduleConfigService->setMakairaSecret($this->_getPostData('shared_secret'));

            $makairaClient = new \GXModules\Makaira\GambioConnect\App\MakairaClient($this->configurationService);
            $connection = LegacyDependencyContainer::getInstance()->get(\Doctrine\DBAL\Connection::class);
            $languageReadService = LegacyDependencyContainer::getInstance()->get(\Gambio\Admin\Modules\Language\Services\LanguageReadService::class);
            $makairaLogger = new \GXModules\Makaira\GambioConnect\App\MakairaLogger();
            $productVariantsRepository = LegacyDependencyContainer::getInstance()->get(\Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository::class);

            (new GambioConnectManufacturerService(
                $makairaClient,
                $languageReadService,
                $connection,
                $makairaLogger,
                $productVariantsRepository
            ))->prepareExport();

            (new GambioConnectCategoryService(
                $makairaClient,
                $languageReadService,
                $connection,
                $makairaLogger,
                $productVariantsRepository
            ))->prepareExport();

            (new GambioConnectProductService(
                $makairaClient,
                $languageReadService,
                $connection,
                $makairaLogger,
                $productVariantsRepository
            ))->prepareExport();

            return new \JsonHttpControllerResponse(['success' => true]);
        }
        return new \JsonHttpControllerResponse(['success' => false]);
    }
}
