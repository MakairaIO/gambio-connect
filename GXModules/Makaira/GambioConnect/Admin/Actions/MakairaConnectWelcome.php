<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\Makaira\GambioConnect\Admin\Services\ModuleStatusService;
use GXModules\Makaira\GambioConnect\Admin\Services\StripeService;

/**
 * Class MakairaConnectWelcome
 *
 * @package GXModules\Makaira\GambioConnect\App\Actions
 */
class MakairaConnectWelcome extends AdminModuleAction
{
    public function __construct(protected ModuleStatusService $moduleStatusService)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {

        if ($this->moduleStatusService->isInSetup() || $this->moduleStatusService->isSetUp()) {
            return $response->withRedirect($this->url->admin() . '/makaira/account', 302);
        }

        $pageTitle    = 'Makaira Gambio FAQs';
        $templatePath = __DIR__ . '/../ui/template/welcome.html';

        $stripeCheckoutUrl = HTTP_SERVER . DIR_WS_CATALOG . 'admin/makaira/gambio-connect/stripe-checkout';

        $data = [
            'welcomeJs' => "{$this->url->base()}/GXModules/Makaira/GambioConnect/Admin/ui/assets/welcome.js",
            'welcomeCss' => "{$this->url->base()}/GXModules/Makaira/GambioConnect/Admin/ui/assets/welcome.css",
            'logo'       => "{$this->url->base()}/GXModules/Makaira/GambioConnect/Admin/ui/assets/logo.svg",
            'packages'  => $this->getPackages(),
            'stripeCheckoutUrl' => $stripeCheckoutUrl,
            'totalPackages' => [
                'price' =>  45,
                'priceId'   =>  '<package_stripe_price>'
            ],
            'bundlePackage'   =>  [
                'subscription'  =>  StripeService::BUNDLE_PRICE_ID,
                'card_color'    =>  'aube',
                'card_type'     => 'red',
                'heading' => $this->getTranslateSection('PACKAGE_PROFESSIONAL_HEADING'),
                'desc'  => $this->getTranslateSection('PACKAGE_PROFESSIONAL_DESC'),
                'icon'  => 'box',
                'price' => "30",
                'priceId'   =>  StripeService::BUNDLE_PRICE_ID,
                'orderNowBtn' =>  true,
                'features'  => [],
            ],
            'companies' =>  [
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/Admin/ui/assets/hark.png",
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/Admin/ui/assets/fielmann.png",
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/Admin/ui/assets/ludwing_von_kapff.svg",
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/Admin/ui/assets/geliebteszuhause.png",
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/Admin/ui/assets/wallart.svg",
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/Admin/ui/assets/pets_premium.png",
            ]
        ];
        $template = $this->render($pageTitle, $templatePath, $data);

        return $response->write($template);
    }

    private function getTranslateSection(string $phrase)
    {
        return $this->translate($phrase, 'gambio_welcome') ?? '';
    }

    private function getPackages()
    {
        // Package Search
        $package1 = [
            'subscription'  =>  StripeService::SEARCH_PRICE_ID,
            'card_type'     => 'yellow',
            'heading' => $this->getTranslateSection('PACKAGE_0_HEADING'),
            'desc'  => $this->getTranslateSection('PACKAGE_0_DESC'),
            'icon'  => 'search',
            'default' => true,
            'depends_on'  =>  [],
            'features'  => [
                [
                    'name'  =>  $this->getTranslateSection('PACKAGE_0_FEATURES_NAME_1'),
                    'desc'  =>  $this->getTranslateSection('PACKAGE_0_FEATURES_DESC_1')
                ],
                [
                    'name'  =>  $this->getTranslateSection('PACKAGE_0_FEATURES_NAME_2'),
                    'desc'  =>  $this->getTranslateSection('PACKAGE_0_FEATURES_DESC_2')
                ],
                [
                    'name'  =>  $this->getTranslateSection('PACKAGE_0_FEATURES_NAME_3'),
                    'desc'  =>  $this->getTranslateSection('PACKAGE_0_FEATURES_DESC_3')
                ],
                [
                    'name'  =>  $this->getTranslateSection('PACKAGE_0_FEATURES_NAME_4'),
                    'desc'  =>  $this->getTranslateSection('PACKAGE_0_FEATURES_DESC_4')
                ]
            ],
            'price' => "30",
            'priceId'   =>  StripeService::SEARCH_PRICE_ID,
            'trial' =>  $this->getTranslateSection('PACKAGE_0_TRIAL'),
            'trialbtn'  =>  $this->getTranslateSection('PACKAGE_0_TRIAL_BTN')
        ];

        // Package Recommendation
        $package2 = [
            'subscription'  =>  StripeService::RECOMMENDATIONS_PRICE_ID,
            'card_type'     => 'aube',
            'heading' => $this->getTranslateSection('PACKAGE_1_HEADING'),
            'desc'  => $this->getTranslateSection('PACKAGE_1_DESC'),
            'icon'  => 'comment-plus',
            'default' => false,
            'depends_on'  =>  [],
            'features'  => array(
                [
                    'name'  =>  $this->getTranslateSection('PACKAGE_1_FEATURES_NAME_1'),
                    'desc'  =>  $this->getTranslateSection('PACKAGE_1_FEATURES_DESC_1')
                ],
                [
                    'name'  =>  $this->getTranslateSection('PACKAGE_1_FEATURES_NAME_2'),
                    'desc'  =>  $this->getTranslateSection('PACKAGE_1_FEATURES_DESC_2')
                ],
                [
                    'name'  =>  $this->getTranslateSection('PACKAGE_1_FEATURES_NAME_3'),
                    'desc'  =>  $this->getTranslateSection('PACKAGE_1_FEATURES_DESC_3')
                ],
            ),
            'price' => "30",
            'priceId'   =>  StripeService::RECOMMENDATIONS_PRICE_ID,
            'trial' =>  $this->getTranslateSection('PACKAGE_1_TRIAL'),
            'trialbtn'  =>  $this->getTranslateSection('PACKAGE_1_TRIAL_BTN')
        ];

        // Package Advertising spaces
        $package3 = [
            'subscription'  =>  StripeService::ADS_PRICE_ID,
            'card_type'     => 'cyan',
            'heading' => $this->getTranslateSection('PACKAGE_2_HEADING'),
            'desc'  => $this->getTranslateSection('PACKAGE_2_DESC'),
            'icon'  => 'megaphone',
            'default' => false,
            'depends_on'  =>  [StripeService::SEARCH_PRICE_ID],
            'features'  => array(
                [
                    'name'  =>  $this->getTranslateSection('PACKAGE_2_FEATURES_NAME_1'),
                    'desc'  =>  $this->getTranslateSection('PACKAGE_2_FEATURES_DESC_1')
                ],
                [
                    'name'  =>  $this->getTranslateSection('PACKAGE_2_FEATURES_NAME_2'),
                    'desc'  =>  $this->getTranslateSection('PACKAGE_2_FEATURES_DESC_2')
                ],
            ),
            'price' => "30",
            'priceId'   =>  StripeService::ADS_PRICE_ID,
            'trial' =>  $this->getTranslateSection('PACKAGE_2_TRIAL'),
            'trialbtn'  =>  $this->getTranslateSection('PACKAGE_2_TRIAL_BTN')
        ];

        return array($package1, $package2, $package3);
    }
}
