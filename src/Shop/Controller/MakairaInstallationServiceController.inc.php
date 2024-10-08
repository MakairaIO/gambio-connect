<?php

use Doctrine\DBAL\Connection;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Language\Services\LanguageService;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;
use GXModules\MakairaIO\MakairaConnect\App\MakairaClient;
use GXModules\MakairaIO\MakairaConnect\App\MakairaLogger;

/**
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

        $this->logger = new MakairaLogger;

        $this->moduleConfigService = new ModuleConfigService($this->configurationService);
    }

    public function actionDefault(): \JsonHttpControllerResponse
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $this->logger->debug('Makaira Installation Service Callback', [
            'data' => $data,
        ]);

        $this->moduleConfigService->setMakairaUrl($data['url']);

        $this->moduleConfigService->setMakairaInstance($data['instance']);

        $this->moduleConfigService->setMakairaSecret($data['sharedSecret']);

        $this->moduleConfigService->setMakairaCronJobActive();

        $this->moduleConfigService->setMakairaCronJobInterval();

        $makairaClient = new MakairaClient(LegacyDependencyContainer::getInstance()
            ->get(ConfigurationService::class));

        $languageService = LegacyDependencyContainer::getInstance()->get(LanguageService::class);

        $connection = LegacyDependencyContainer::getInstance()->get(Connection::class);

        $makairaLogger = MainFactory::create(MakairaLogger::class);

        $gambioConnectService = new GambioConnectService($makairaClient, $languageService, $connection,
            $makairaLogger);

        $gambioConnectService->getManufacturerService()->prepareExport();

        $gambioConnectService->getCategoryService()->prepareExport();

        $gambioConnectService->getProductService()->prepareExport();

        return new \JsonHttpControllerResponse(['success' => true]);
    }
}
