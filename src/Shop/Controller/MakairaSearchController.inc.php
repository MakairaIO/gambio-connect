<?php

namespace Controller;

use ContentViewInterface;
use Gambio\Core\Language\Services\LanguageService;
use GXModules\MakairaIO\MakairaConnect\App\Core\MakairaRequest;
use HttpContextReaderInterface;
use HttpControllerResponse;
use HttpResponseProcessorInterface;
use HttpViewController;
use LegacyDependencyContainer;
use MainFactory;

/**
 * @link http://shop-url.de/shop.php?do=MakairaSearch/GetAutosuggest
 */
class MakairaSearchController extends HttpViewController
{
    private $makairaRequest;

    private $configurationStorage;

    private $languageService;

    public function __construct(
        HttpContextReaderInterface $httpContextReader,
        HttpResponseProcessorInterface $httpResponseProcessor,
        ContentViewInterface $defaultContentView,
    ) {
        parent::__construct($httpContextReader, $httpResponseProcessor, $defaultContentView);

        $this->languageService = LegacyDependencyContainer::getInstance()->get(LanguageService::class);
        $this->configurationStorage = MainFactory::create('GXModuleConfigurationStorage', 'Makaira/MakairaConnect');

        $makairaUrl = $this->configurationStorage->get('makairaUrl');
        $makairaInstance = $this->configurationStorage->get('makairaInstance');
        $languageId = $_SESSION['languages_id'] ?? '2';
        $languageId = (int) $languageId;
        $language = $this->languageService->getLanguageById(2);
        $this->makairaRequest = new MakairaRequest($makairaUrl, $makairaInstance, $language->code());
    }

    public function actionGetAutosuggest()
    {
        $keyword = $this->_getQueryParameter('keyword');
        $result = $this->makairaRequest->fetchAutoSuggest($keyword);

        $aProducts = isset($result['product']) ? $result['product']['items'] : [];
        $aCategories = isset($result['category']) ? $result['category']['items'] : [];
        $aManufacturers = isset($result['manufacturer']) ? $result['manufacturer']['items'] : [];
        $aLinks = isset($result['links']) ? $result['links']['items'] : [];
        $aPages = isset($result['page']) ? $result['page']['items'] : [];

        $this->contentView->set_template_dir(__DIR__.DIRECTORY_SEPARATOR);
        $html = $this->_render('../ui/template/autosuggest.html', [
            'result' => [
                'products' => $aProducts,
                'categories' => $aCategories,
                'manufacturers' => $aManufacturers,
                'links' => $aLinks,
                'pages' => $aPages,
            ],
            'MAKAIRA_ACTIVE_SEARCH' => $this->configurationStorage->get('makairaActiveSearch'),
        ]);

        return new HttpControllerResponse($html);
    }
}
