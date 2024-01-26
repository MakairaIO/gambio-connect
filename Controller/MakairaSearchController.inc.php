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
 * @link http://shop-url.de/shop.php?do=MakairaHttpRender/GetAutosuggest
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
		$result = $this->makairaRequest->fetchAutoSuggest('a');

		// get product results
		$aProducts = [];
		foreach ($result['product']->items as $document) {
				var_dump($document);
				// $aProducts[] = $this->loadProductItem($document);
		}
		// filter out empty values
		$aProducts = array_filter($aProducts);

		// get category results
		$aCategories = [];
		if ($result['category']) {
				foreach ($result['category']->items as $document) {
						// $aCategories[] = $this->prepareCategoryItem($document);
				}
		}
		// filter out empty values
		$aCategories = array_filter($aCategories);

		// get manufacturer results
		$aManufacturers = [];
		if ($result['manufacturer']) {
				foreach ($result['manufacturer']->items as $document) {
						// $aManufacturers[] = $this->prepareManufacturerItem($document);
				}
		}
		// filter out empty values
		$aManufacturers = array_filter($aManufacturers);

		// get searchable links results
		$aLinks = [];
		if ($result['links']) {
				foreach ($result['links']->items as $document) {
						// $aLinks[] = $this->prepareLinkItem($document);
				}
		}
		// filter out empty values
		$aLinks = array_filter($aLinks);

		// get suggestion results
		$aSuggestions = [];
		if ($result['suggestion']) {
				foreach ($result['suggestion']->items as $document) {
						// $aSuggestions[] = $this->prepareSuggestionItem($document);
				}
		}
		// filter out empty values
		$aSuggestions = array_filter($aSuggestions);

		// return [
		// 		'count'         => count($aProducts),
		// 		'products'      => $aProducts,
		// 		'productCount'  => $result['product']->total,
		// 		'categories'    => $aCategories,
		// 		'manufacturers' => $aManufacturers,
		// 		'links'         => $aLinks,
		// 		'suggestions'   => $aSuggestions,
		// ];

		// var_dump($result);

		# set the template directory to the current directory
		$this->contentView->set_template_dir(__DIR__ . DIRECTORY_SEPARATOR);
		$html = $this->_render('../ui/template/autosuggest.html', array('param1' => 'aaaa', 'param2' => 'World'));

		return new HttpControllerResponse($html);
	}
}
