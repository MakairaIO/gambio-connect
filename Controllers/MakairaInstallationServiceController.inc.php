<?php

//namespace GXModules\Makaira\GambioConnect\Controllers;

use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 *
 * @link http://shop-url.de/shop.php?do=MakairaInstallationService
 */
class MakairaInstallationServiceController extends HttpViewController
{
    private ConfigurationService $configurationService;
    public function __construct(HttpContextReaderInterface $httpContextReader, HttpResponseProcessorInterface $httpResponseProcessor, ContentViewInterface $defaultContentView)
    {
        parent::__construct($httpContextReader, $httpResponseProcessor, $defaultContentView);

        $this->configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
    }

    public function actionDefault(): \JsonHttpControllerResponse
    {
        if($this->configurationService->find('modules/MakairaGambioConnect/stripeCheckoutSession')->value() === $this->_getPostData('stripeCheckoutId')) {

            $this->configurationService->save('modules/MakairaGambioConnect/makairaUrl', $this->_getPostData('url'));

            $this->configurationService->save('modules/MakairaGambioConnect/makairaInstance', $this->_getPostData('instance'));

            $this->configurationService->save('modules/MakairaGambioConnect/makairaSecret', $this->_getPostData('sharedSecret'));

        }

        return new \JsonHttpControllerResponse(['success' => true]);
    }
}
