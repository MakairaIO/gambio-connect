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
    
    private function loadCrossSelling() {
        $this->set_content_template('product_info_cross_selling.html');
        $requestData = $this->makairaRequest->fetchRecommendations($this->coo_product->data['products_id']);
        return $requestData['items'];
    }
    
    private function loadReverseCrossSelling() {
        $this->set_content_template('product_info_reverse_cross_selling.html');
        $requestData = $this->makairaRequest->fetchRecommendations($this->coo_product->data['products_id']);
        return $requestData['items'];
    }
}