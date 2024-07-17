<?php

use GXModules\MakairaIO\MakairaConnect\App\Core\MakairaRequest;

// phpcs:ignore
class MakairaCrossSellingThemeContentView extends CrossSellingThemeContentView
{
    private $moduleStatusService;

    private $moduleConfigService;

    private $makairaRequest;

    public function __construct()
    {
        parent::__construct();

        $configurationService = LegacyDependencyContainer::getInstance()->get(
            \Gambio\Core\Configuration\Services\ConfigurationService::class
        );

        $this->moduleConfigService = new \GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService(
            $configurationService
        );

        $this->moduleStatusService = new \GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleStatusService(
            $this->moduleConfigService
        );

        $makairaUrl = $this->moduleConfigService->getMakairaUrl();

        $makairaInstance = $this->moduleConfigService->getMakairaInstance();

        $this->makairaRequest = new MakairaRequest($makairaUrl, $makairaInstance, $_SESSION['language_code'] ?? 'de');
    }

    // phpcs:ignore
    protected function get_data(): array
    {
        if (
            $this->moduleStatusService->isSetUp()
            && $this->moduleStatusService->isActive()
        ) {
            return match ($this->type) {
                'cross_selling' => $this->loadCrossSelling(),
                'reverse_cross_selling' => $this->loadReverseCrossSelling(),
                default => []
            };
        } else {
            return parent::get_data() ?? [];
        }
    }

    private function mapMakairaResponse(array $items): array
    {
        $preparedData = [];

        foreach ($items as $item) {
            $fields = $item['fields'];
            $preparedData[] =
                [
                    'products_id' => $item['id'],
                    'products_fsk18' => $fields['fsk_18'] ?? false,
                    'products_tax_class_id' => $fields['tax_class_id'],
                    'products_image' => $fields['picture_url_main'],
                    'products_name' => $fields['title'],
                    'products_short_description' => $fields['shortdesc'],
                    'products_long_description' => $fields['longdesc'],
                    'products_price' => $fields['price'],
                    'gm_alt_text' => $fields['gm_alt_text'],
                    'products_vpe' => $fields['products_vpe'],
                    'products_vpe_value' => $fields['products_vpe_value'],
                    'products_vpe_status' => $fields['products_vpe_status'],
                    'sort_order' => $fields['sort_order']
                ];
        }

        $data = [];

        foreach ($preparedData as $preparedDataItem) {
            $data[0]['PRODUCTS'][] =  array_merge(
                $this->coo_product->buildDataArray($preparedDataItem),
                [
                    'PRODUCTS_IMAGE' => $preparedDataItem['products_image']
                ]
            );
        }
        return $data;
    }

    private function loadCrossSelling(): array
    {
        if (empty($this->moduleConfigService->getRecoCrossSelling()) || empty($this->moduleStatusService->getModuleConfigService()->getRecoCrossSelling())) {
            return [];
        }
        $this->set_content_template('product_info_cross_selling.html');
        $requestData = $this->makairaRequest->fetchRecommendations($this->coo_product->data['products_id'], $this->moduleConfigService->getRecoCrossSelling());
        return $this->mapMakairaResponse($requestData['items']);
    }

    private function loadReverseCrossSelling(): array
    {
        if (empty($this->moduleConfigService->getRecoReverseCrossSelling()) || empty($this->moduleStatusService->getModuleConfigService()->getRecoReverseCrossSelling())) {
            return [];
        }
        $this->set_content_template('product_info_reverse_cross_selling.html');
        $requestData = $this->makairaRequest->fetchRecommendations($this->coo_product->data['products_id'], $this->moduleConfigService->getRecoReverseCrossSelling());
        return $this->mapMakairaResponse($requestData['items'])[0]['PRODUCTS'];
    }
}
