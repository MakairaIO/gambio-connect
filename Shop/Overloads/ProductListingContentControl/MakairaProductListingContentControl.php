<?php

class MakairaProductListingContentControl extends ProductListingContentControl
{

    private \GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService $moduleConfigService;

    private \GXModules\Makaira\GambioConnect\App\MakairaClient $makairaClient;

    public function __construct()
    {
        parent::__construct();

        $configurationService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationService::class);

        $this->moduleConfigService = new \GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService($configurationService);

        $this->makairaClient = new \GXModules\Makaira\GambioConnect\App\MakairaClient($configurationService);
    }

    public function build_search_result_sql()
    {
        dd($this);
    }

    private function getCategory($categoryId) {
        return $this->makairaClient->getCategory($categoryId, $this->determine_max_display_search_results());
    }

    public function get_category_data_array()
    {
        $result = $this->getCategory($this->current_category_id);

        $category = $result->category->items[0];

        return [
            'description_bottom' => trim($category->categories_description_bottom) === '<br />' ? '' : $category->categories_description_bottom,
            'description' => trim($category->categories_description) === '<br />' ? '' : $category->categories_description,
            'name' => $category->categories_name,
            'heading_title' => $category->categories_heading_title,
            'image_alt_text' => $category->categories_heading_title,
            'image' => '',
            'show_categories_image_in_description' => $category->show_categories_image_in_description,
            'gm_show_attributes' => $category->gm_show_attributes,
            'listing_template' => $category->listing_template,
            'view_mode_tiled' => $category->view_mode_tiled,
        ];
    }

    public function extend_proceed($p_action)
    {
        $this->sql_query = null;

        $result = $this->getCategory($this->current_category_id);

        $category = $result->category->items[0];

        $t_category_data_array = $this->get_category_data_array();

        $t_category_name = $t_category_data_array['name'];
        $t_category_heading_title = $t_category_data_array['heading_title'];
        $t_category_image_alt_text = $t_category_data_array['image_alt_text'];
        $t_category_image = $t_category_data_array['image'];
        $t_show_category_image = $t_category_data_array['show_categories_image_in_description'] ?? null;
        $t_categories_description = $t_category_data_array['description'];
        $t_categories_description_bottom = $t_category_data_array['description_bottom'];
        $t_show_quantity = $t_category_data_array['show_quantity'];
        $t_category_show_quantity = $t_category_data_array['category_show_quantity'];


        $this->product_listing_view->set_('category_description', $t_categories_description);
        $this->product_listing_view->set_('category_description_bottom', $t_categories_description_bottom);
        $this->product_listing_view->set_('category_heading_title', $t_category_heading_title);
        $this->product_listing_view->set_('category_image_alt_text', $t_category_image_alt_text);
        $this->product_listing_view->set_('category_image', $t_category_image);
        $this->product_listing_view->set_('showCategoriesImageInDescription', $t_show_category_image);
        $this->product_listing_view->set_('category_name', $t_category_name);
        $this->product_listing_view->set_('show_quantity', $t_show_quantity);

        $this->product_listing_view->set_('cache_id_parameter_array', $this->cache_id_parameter_array);
        $this->product_listing_view->set_('category_id', $this->current_category_id);
        $this->product_listing_view->set_('c_path', $this->c_path);
        $this->product_listing_view->set_('coo_mn_data_container', $this->coo_mn_data_container);

        $products = $result->product->items;

        $mappedProducts = [];

        foreach($products as $product) {
            $mappedProducts[] = [
                'products_fsk18' => $product->fields->fsk18 ?? false,
                'products_shippingtime' => 0,
                'use_properties_combis_shipping_time' => 0,
                'products_model' => '',
                'products_ean' => $product->fields->ean,
                'products_name' => $product->fields->title,
                'manufacturers_name' => $product->fields->manufacturer_title,
                'products_quantity' => $product->fields->stock,
                'products_image' => $product->fields->picture_url_main,
                'products_image_w' => 0,
                'products_image_h' => 0,
                'products_weight' => 0,
                'gm_show_weight' => 1,
                'products_short_description' => $product->fields->shortdesc,
                'products_description' => $product->fields->longdesc,
                'gm_alt_text' => $product->fields->gm_alt_text,
                'products_meta_description' => $product->fields->products_meta_description ?? '',
                'products_id' => $product->id,
                'manufacturers_id' => $product->fields->manufacturerid,
                'products_price' => $product->fields->price,
                'products_vpe' => $product->fields->products_vpe,
                'products_vpe_status' => $product->fields->products_vpe_status,
                'products_vpe_value' => $product->fields->products_vpe_value,
                'products_discount_allowed' => 0,
                'products_tax_class_id' => $product->fields->tax_class_id,
                'cat_url' => '',
                'expired_date' => '',
                'ID' => $product->id
            ];
        }
    }
}