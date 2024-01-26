<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class GambioConnectWelcome
 *
 * @package GXModules\Makaira\GambioConnect\App\Actions
 */
class GambioConnectWelcome extends AdminModuleAction
{
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $pageTitle    = 'Makaira Gambio FAQs';
        $templatePath = __DIR__ . '/../../ui/template/welcome.html';

        $data = [
            'overviewJs' => "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/overview.js",
            'welcomeCss' => "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/welcome.css",
            'logo'       => "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/logo.svg",
            'packages'  => $this->getPackages(),
            'totalPackages' => [
                'price' =>  45
            ],
            'bundlePackage'   =>  [
                'subscription'  =>  'prod_PQDcothTDeyG5J',
                'card_color'    =>  'aube',
                'card_type'     => 'red',
                'heading' => $this->getTranslateSection('PACKAGE_PROFESSIONAL_HEADING'),
                'desc'  => $this->getTranslateSection('PACKAGE_PROFESSIONAL_DESC'),
                'icon'  => 'box',
                'price' => "30",
                'orderNowBtn' =>  true,
            ],
            'companies' =>  [
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/hark.png",
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/fielmann.png",
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/ludwing_von_kapff.svg",
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/geliebteszuhause.png",
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/wallart.svg",
                "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/pets_premium.png",
            ]
        ];
        $template = $this->render($pageTitle, $templatePath, $data);

        return $response->write($template);
    }

    private function getTranslateSection(string $phrase) {
        return $this->translate($phrase, 'gambio_welcome') ?? '';
    }

    private function getPackages()
    {
        // Package Search
        $package1 = [
            'subscription'  =>  'prod_POh7K4aYgT4jDg',
            'card_type'     => 'yellow',
            'heading' => $this->getTranslateSection('PACKAGE_0_HEADING'),
            'desc'  => $this->getTranslateSection('PACKAGE_0_DESC'),
            'icon'  => 'search',
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
            'trial' =>  $this->getTranslateSection('PACKAGE_0_TRIAL'),
            'trialbtn'  =>  $this->getTranslateSection('PACKAGE_0_TRIAL_BTN')
        ];

        // Package Recommendation
        $package2 = [
            'subscription'  =>  'prod_PQDMJ9dlGvv8Wj',
            'card_type'     => 'aube',
            'heading' => $this->getTranslateSection('PACKAGE_1_HEADING'),
            'desc'  => $this->getTranslateSection('PACKAGE_1_DESC'),
            'icon'  => 'comment-plus',
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
            'trial' =>  $this->getTranslateSection('PACKAGE_1_TRIAL'),
            'trialbtn'  =>  $this->getTranslateSection('PACKAGE_1_TRIAL_BTN')
        ];

        // Package Advertising spaces
        $package3 = [
            'subscription'  =>  'prod_PQDYvloTtoO2zs',
            'card_type'     => 'cyan',
            'heading' => $this->getTranslateSection('PACKAGE_2_HEADING'),
            'desc'  => $this->getTranslateSection('PACKAGE_2_DESC'),
            'icon'  => 'megaphone',
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
            'trial' =>  $this->getTranslateSection('PACKAGE_2_TRIAL'),
            'trialbtn'  =>  $this->getTranslateSection('PACKAGE_2_TRIAL_BTN')
        ];

        return array($package1, $package2, $package3);
    }
}
