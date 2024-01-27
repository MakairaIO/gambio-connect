<?php

/* --------------------------------------------------------------
   MakairaSearchController.php 2016-02-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
// namespace GXModules\Makaira\GambioConnect\Controller;

use GXModules\Makaira\GambioConnect\App\Core\MakairaRequest;
use Gambio\Core\Language\Services\LanguageService;

/**
 *
 * @link http://shop-url.de/shop.php?do=MakairaSearch/GetAutosuggest
 */
class MakairaSearchController extends HttpViewController
{
	private $makairaRequest;
	private $configurationStorage;
	private $languageService;

	function __construct(
		HttpContextReaderInterface $httpContextReader,
		HttpResponseProcessorInterface $httpResponseProcessor,
		ContentViewInterface $defaultContentView,
	) {
		parent::__construct($httpContextReader, $httpResponseProcessor, $defaultContentView);

		$this->languageService = LegacyDependencyContainer::getInstance()->get(LanguageService::class);
		$this->configurationStorage = MainFactory::create('GXModuleConfigurationStorage', 'Makaira/GambioConnect');

		$makairaUrl = $this->configurationStorage->get('makairaUrl');
		$makairaInstance = $this->configurationStorage->get('makairaInstance');
		$languageId = $_SESSION['languages_id'] ?? '2';
		$languageId = (int)$languageId;
		$language = $this->languageService->getLanguageById(2);
		$this->makairaRequest = new MakairaRequest($makairaUrl, $makairaInstance, $language->code());
	}

	public function actionGetAutosuggest()
	{
		$keyword = $this->_getQueryParameter('keyword');
		$result = $this->makairaRequest->fetchAutoSuggest($keyword);

		$aProducts = $result['product']['items'];
		$aCategories = $result['category']['items'];
		$aManufacturers = $result['manufacturer']['items'];
		$aLinks = $result['links']['items'];
		$aSuggestions = $result['suggestion']['items'];

		$this->contentView->set_template_dir(__DIR__ . DIRECTORY_SEPARATOR);
		$html = $this->_render('../ui/template/autosuggest.html', array(
			'result' => [
				'products'      => $aProducts,
				'productCount'  => $result['product']->total,
				'categories'    => $aCategories,
				'manufacturers' => $aManufacturers,
				'links'         => $aLinks,
				'suggestions'   => $aSuggestions,
			],
			'MAKAIRA_ACTIVE_SEARCH' => $this->configurationStorage->get('makairaActiveSearch'),
		));

		return new HttpControllerResponse($html);
	}

	
}
