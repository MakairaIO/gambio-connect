<?php

use ContentViewInterface;
use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Language\Services\LanguageService;
use GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\Makaira\GambioConnect\App\MakairaClient;
use GXModules\Makaira\GambioConnect\App\MakairaLogger;
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

            $makairaClient = new MakairaClient(
                LegacyDependencyContainer::getInstance()->get(ConfigurationService::class)
            );

            $languageService = LegacyDependencyContainer::getInstance()->get(LanguageService::class);

            $connection = LegacyDependencyContainer::getInstance()->get(Connection::class);

            $makairaLogger = MainFactory::create(MakairaLogger::class);

            $productVariantsRepository = LegacyDependencyContainer::getInstance()->get(ProductVariantsRepository::class);

            (new GambioConnectManufacturerService(
                $makairaClient,
                $languageService,
                $connection,
                $makairaLogger
            ))->prepareExport();

            (new GambioConnectCategoryService(
                $makairaClient,
                $languageService,
                $connection,
                $makairaLogger
            ))->prepareExport();

            (new GambioConnectProductService(
                $makairaClient,
                $languageService,
                $connection,
                $makairaLogger,
                $productVariantsRepository
            ))->prepareExport();

            return new \JsonHttpControllerResponse(['success' => true]);
        }
        return new \JsonHttpControllerResponse(['success' => false]);
    }
}
