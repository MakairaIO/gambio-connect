<?php

use GXModules\Makaira\GambioConnect\App\Core\MakairaRequest;

class MakairaCrossSellingThemeContentView extends CrossSellingThemeContentView
{
    
    private $configurationStorage;
    
    private $makairaRequest;
    public function __construct() {
        parent::__construct();
        
        $this->configurationStorage = MainFactory::create('GXModuleConfigurationStorage', 'Makaira/GambioConnect');
        
        $makairaUrl = $this->configurationStorage->get('makairaUrl');
        $makairaInstance = $this->configurationStorage->get('makairaInstance');
        
        $this->makairaRequest = new MakairaRequest($makairaUrl, $makairaInstance, $_SESSION['language_code'] ?? 'de');
    }
    
    
    protected function get_data()
    {
        return match ($this->type) {
            'cross_selling' => $this->loadCrossSelling(),
            'reverse_cross_selling' => $this->loadReverseCrossSelling(),
            default => []
        };
    }

    private function mapMakairaResponse(array $items): array
    {
        $data = [];
        foreach($items as $item) {
            $fields = $item['fields'];
            $data[]['PRODUCTS'][$item['id']] = array_merge(
                [
                    'products_id' => $item['id'],
                ],
                [
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
                ]

            );
        }
        return $data;
    }
    
    private function loadCrossSelling(): array
    {
        $this->set_content_template('product_info_cross_selling.html');
        $requestData = $this->makairaRequest->fetchRecommendations($this->coo_product->data['products_id']);
        return $this->mapMakairaResponse($requestData['items']);
    }
    
    private function loadReverseCrossSelling(): array
    {
        $this->set_content_template('product_info_reverse_cross_selling.html');
        $requestData = $this->makairaRequest->fetchRecommendations($this->coo_product->data['products_id']);
        return $this->mapMakairaResponse($requestData['items']);
    }
}