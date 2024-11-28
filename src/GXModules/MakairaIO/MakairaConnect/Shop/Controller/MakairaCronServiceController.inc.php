<?php

ini_set('memory_limit', '-1');

use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\MakairaIO\MakairaConnect\App\MakairaClient;
use GXModules\MakairaIO\MakairaConnect\App\MakairaLogger;

/**
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
        $this->logger = new MakairaLogger;

        if (empty($_GET['language'])) {
            return new JsonHttpControllerResponse([
                'error' => 'This action requires language parameter',
            ]);
        }

        $jsonInput = file_get_contents('php://input');

        $this->logger->debug("Makaira CRON Service Controller", [
            'jsonInput' => $jsonInput,
        ]);

        $data = json_decode($jsonInput, true, flags: JSON_THROW_ON_ERROR);

        $this->languageService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Language\Services\LanguageService::class);

        try {
            $this->languageService->getLanguageByCode($_GET['language']);
        } catch (Exception $exception) {
            return new JsonHttpControllerResponse([
                'error' => 'Language '.$_GET['language'].' not found',
            ]);
        }

        $this->configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);

        $this->moduleConfigService = new ModuleConfigService($this->configurationService);

        $this->client = new MakairaClient($this->configurationService);

        $this->connection = LegacyDependencyContainer::getInstance()->get(\Doctrine\DBAL\Connection::class);

        $this->logger->debug('Makaira Export Job Called', [
            'GET_Variables' => $_GET,
            'POST_Variables' => $data,
            'SESSION_Variables' => $_SESSION,
        ]);

        $productVariantsReadService = new \Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsRepository(
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsReader($this->connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsDeleter($this->connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsInserter($this->connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsUpdater($this->connection),
            new \Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsMapper(
                new \Gambio\Admin\Modules\ProductVariant\Services\ProductVariantFactory,
            ),
            LegacyDependencyContainer::getInstance()->get(\Psr\EventDispatcher\EventDispatcherInterface::class)
        );

        $makairaConnectService = new \GXModules\MakairaIO\MakairaConnect\App\GambioConnectService(
            $this->client,
            $this->languageService,
            $this->connection,
            new MakairaLogger,
            $productVariantsReadService
        );

        $this->logger->debug('Front-End Controller called', [
            'changes' => $data
        ]);

        $makairaConnectService->exportToMakaira($data['changes']);

        return new \JsonHttpControllerResponse([
            'success' => true,
        ]);
    }
}
