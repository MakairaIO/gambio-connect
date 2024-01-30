<?php

use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Makaira\GambioConnect\Admin\Services\StripeService;

/**
 * Class SampleHttpController
 *
 * This is a sample http view controller class which present several action methods.
 *
 * IMPORTANT (Instruction to use the sample controller class):
 *
 * Copy this file to the destination directory 'src/GXEngine/Controllers' and register the sample controller
 * in the EnvironmentHttpViewControllerRegistryFactory::_addAvailableControllers method with the following code
 * snippet:
 * 
 *   $registry->set('SampleHttpAction', 'SampleHttpActionController');
 * 
 * Just paste the snippet to the end of the method body.
 *
 * Afterwards, open the UR  http://shop-url.de/shop.php?do=SampleHttpAction or 
 * http://shop-url.de/shop.php?do=SampleHttpAction/XY to delegate to the action methods.
 */
class MakairaCheckoutController extends AdminHttpViewController
{
    
    
    /**
   * Default action method.
   *
   * This method is invoked by the request url: 'start.php?do=MakairaCheckout'
   *
   * @return HttpControllerResponseInterface
   */
  public function actionDefault()
  {
      $stripeService = new StripeService();
      
      $configurationService = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
      
      $stripeService->setConfigurationService($configurationService);
      
      foreach($this->_getPostData('priceIds') as $priceId) {
          $stripeService->addPriceId($priceId);
      }
      
      $session = $stripeService->createCheckoutSession();

    // Execute business logic here!
    return new RedirectHttpControllerResponse($session->url);
  }
}
