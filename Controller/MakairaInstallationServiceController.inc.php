<?php

use ContentViewInterface;
use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Language\Services\LanguageService;
use GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
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

    private \Psr\Log\LoggerInterface $logger;

    public function __construct(
        HttpContextReaderInterface $httpContextReader,
        HttpResponseProcessorInterface $httpResponseProcessor,
        ContentViewInterface $defaultContentView
    ) {
        parent::__construct($httpContextReader, $httpResponseProcessor, $defaultContentView);

        $this->configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);

        $this->logger = new MakairaLogger();

        $this->moduleConfigService = new ModuleConfigService($this->configurationService);
    }

    public function actionDefault(): \JsonHttpControllerResponse
    {
        $this->logger->debug("Makaira Installation Service Callback", [
            'data' => $this->_getPostDataCollection()
        ]);

        if ($this->moduleConfigService->getStripeCheckoutId() === $this->_getPostData('stripeCheckoutId')) {
            $this->moduleConfigService->setMakairaUrl($this->_getPostData('url'));

            $this->moduleConfigService->setMakairaInstance($this->_getPostData('instance'));

            $this->moduleConfigService->setMakairaSecret($this->_getPostData('sharedSecret'));

            $makairaClient = new MakairaClient(
                LegacyDependencyContainer::getInstance()->get(ConfigurationService::class)
            );

            $languageService = LegacyDependencyContainer::getInstance()->get(LanguageService::class);

            $connection = LegacyDependencyContainer::getInstance()->get(Connection::class);

            $makairaLogger = MainFactory::create(MakairaLogger::class);

            $gambioConnectService = new GambioConnectService(
                $makairaClient,
                $languageService,
                $connection,
                $makairaLogger
            );

            $gambioConnectService->getManufacturerService()->prepareExport();

            $gambioConnectService->getCategoryService()->prepareExport();

            $gambioConnectService->getProductService()->prepareExport();

            return new \JsonHttpControllerResponse(['success' => true]);
        }
        return new \JsonHttpControllerResponse(['success' => false]);
    }
}