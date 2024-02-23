<?php

class MakairaProductListingContentControl extends ProductListingContentControl
{
    private \GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService $moduleConfigService;

    private \GXModules\Makaira\GambioConnect\App\MakairaClient $makairaClient;

    private $products = [];

    public function __construct()
    {
        parent::__construct();

        $configurationService = LegacyDependencyContainer::getInstance()->get(
            \Gambio\Core\Configuration\Services\ConfigurationService::class
        );

        $this->moduleConfigService = new \GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService(
            $configurationService
        );

        $this->makairaClient = new \GXModules\Makaira\GambioConnect\App\MakairaClient($configurationService);
    }

    private function getCategory($categoryId)
    {
        return $this->makairaClient->getCategory($categoryId, $this->determine_max_display_search_results());
    }

    public function get_category_data_array()
    {
        $result = $this->getCategory($this->current_category_id);

        $category = $result->category->items[0];

        return [
            'description_bottom' => trim(
                $category->fields->categories_description_bottom
            ) === '<br />' ? '' : $category->fields->categories_description_bottom,
            'description' => trim(
                $category->fields->categories_description
            ) === '<br />' ? '' : $category->fields->categories_description,
            'name' => $category->fields->categories_name,
            'heading_title' => $category->fields->categories_heading_title,
            'image_alt_text' => $category->fields->categories_heading_title,
            'image' => '',
            'show_categories_image_in_description' => $category->fields->show_categories_image_in_description,
            'gm_show_attributes' => $category->fields->gm_show_attributes,
            'listing_template' => $category->fields->listing_template,
            'view_mode_tiled' => $category->fields->view_mode_tiled,
            'show_quantity' => $category->fields->gm_show_qty_info
        ];
    }
}
