<?php
\Stripe\Stripe::setApiKey('<STRIPE API KEY>');
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
    $priceId = $this->_getPostData('priceId');

    $session = \Stripe\Checkout\Session::create([
      'payment_method_types' => ['card'],
      'line_items' => [
        [
          'price' => $priceId, // Replace with the actual price ID
          'quantity' => 1,
        ],
      ],
      'mode' => 'subscription',
      'success_url' => 'http://0.0.0.0:2001/success',
      'cancel_url' => 'http://0.0.0.0:2001/cancel',
    ]);

    // Execute business logic here!
    return new RedirectHttpControllerResponse($session->url);
  }
}
