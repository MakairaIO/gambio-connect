<?php

use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\MakairaIO\MakairaConnect\App\MakairaClient;
use GXModules\MakairaIO\MakairaConnect\App\MakairaLogger;

/**
 *
 * @link http://shop-url.de/shop.php?do=MakairaCronService/doExport?language=de|en|...
 */
class MakairaCronServiceController extends HttpViewController
{
    private ConfigurationService $configurationService;

    private ModuleConfigService $moduleConfigService;

    private \GXModules\MakairaIO\MakairaConnect\App\MakairaClient $client;

    private MakairaLogger $logger;

    private \Gambio\Core\Language\Services\LanguageService $languageService;

    private \Doctrine\DBAL\Connection $connection;

    public function __construct(
        HttpContextReaderInterface $httpContextReader,
        HttpResponseProcessorInterface $httpResponseProcessor,
        ContentViewInterface $defaultContentView
    ) {
        parent::__construct($httpContextReader, $httpResponseProcessor, $defaultContentView);
    }

    public function actionDoExport(): \JsonHttpControllerResponse
    {
        $this->configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);

        $this->logger = new MakairaLogger();

        $this->moduleConfigService = new ModuleConfigService($this->configurationService);

        $this->client = new MakairaClient($this->configurationService);

        $this->languageService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Language\Services\LanguageService::class);

        $this->connection = LegacyDependencyContainer::getInstance()->get(\Doctrine\DBAL\Connection::class);

        $this->logger->debug('Makaira Export Job Called', [
            'GET_Variables' => $_GET['language'],
            'SESSION_Variables' => $_SESSION
        ]);

        $productVariantsReadService = new \Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsRepository(
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsReader($this->connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsDeleter($this->connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsInserter($this->connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsUpdater($this->connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsMapper(
                new \Gambio\Admin\Modules\ProductVariant\Services\ProductVariantFactory(),
            ),
            LegacyDependencyContainer::getInstance()->get(\Psr\EventDispatcher\EventDispatcherInterface::class)
        );

        $makairaConnectService = new \GXModules\MakairaIO\MakairaConnect\App\GambioConnectService(
            $this->client,
            $this->languageService,
            $this->connection,
            new MakairaLogger(),
            $productVariantsReadService
        );

        $makairaConnectService->getManufacturerService()->export();

        $makairaConnectService->getCategoryService()->export();

        $makairaConnectService->getProductService()->export();

        return new \JsonHttpControllerResponse([
            'success' => true
        ]);
    }
}