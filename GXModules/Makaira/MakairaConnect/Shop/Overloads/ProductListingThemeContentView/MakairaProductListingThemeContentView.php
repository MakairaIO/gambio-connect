<?php

class MakairaProductListingThemeContentView extends ProductListingThemeContentView
{
    private \GXModules\Makaira\MakairaConnect\Admin\Services\ModuleConfigService $moduleConfigService;

    private \GXModules\Makaira\MakairaConnect\App\MakairaClient $makairaClient;

    public function __construct($p_template = 'default')
    {
        parent::__construct($p_template);

        $configurationService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationService::class);

        $this->makairaClient = new \GXModules\Makaira\MakairaConnect\Admin\Actions\App\MakairaClient($configurationService);
    }

    public function prepare_data()
    {
        parent::prepare_data();


        $this->set_content_data('CATEGORIES_ID', $this->category_id);
        $this->set_content_data('CATEGORIES_DESCRIPTION', $this->category_description);
        $this->set_content_data('CATEGORIES_DESCRIPTION_BOTTOM', $this->category_description_bottom);
        $this->set_content_data('CATEGORIES_GM_ALT_TEXT', htmlspecialchars_wrapper($this->category_image_alt_text));
        $this->set_content_data(
            'CATEGORIES_HEADING_TITLE',
            htmlspecialchars_wrapper($this->category_heading_title)
        );
        $this->set_content_data('SHOW_CATEGORIES_IMAGE_IN_DESCRIPTION', $this->showCategoriesImageInDescription);
        $this->set_content_data('CATEGORIES_IMAGE', $this->category_image);
        $this->set_content_data('CATEGORIES_NAME', htmlspecialchars_wrapper($this->category_name));

        $t_start_count_value = $this->products_per_page;
        $t_count_value_2 = $t_start_count_value * 2;
        $t_count_value_3 = $t_start_count_value + $t_count_value_2;
        $t_count_value_4 = $t_count_value_3 * 2;
        $t_count_value_5 = $t_count_value_4 * 2;
        $this->set_content_data('COUNT_VALUE_1', $t_start_count_value);
        $this->set_content_data('COUNT_VALUE_2', $t_count_value_2);
        $this->set_content_data('COUNT_VALUE_3', $t_count_value_3);
        $this->set_content_data('COUNT_VALUE_4', $t_count_value_4);
        $this->set_content_data('COUNT_VALUE_5', $t_count_value_5);

        $this->set_content_data('FILTER_SELECTION', $this->filter_selection_html);
        $this->set_content_data('get_params_hidden_data', $this->get_params_hidden_data_array);

        if ($this->show_quantity === true) {
            $this->set_content_data('GM_SHOW_QTY', '1');
        } else {
            $this->set_content_data('GM_SHOW_QTY', '0');
        }

        $this->set_content_data('gm_manufacturers_id', $this->manufacturers_id);
        $this->set_content_data('HIDDEN_QTY_NAME', 'products_qty');
        $this->set_content_data('HIDDEN_QTY_VALUE', '1');

        if ($this->listing_count !== null) {
            $this->set_content_data('ITEM_COUNT', htmlspecialchars_wrapper($this->listing_count));
        }

        $this->set_content_data('manufacturers_data', $this->manufacturers_data_array);
        $this->set_content_data('MANUFACTURER_DROPDOWN', $this->manufacturers_dropdown);
        $this->set_content_data('module_content', $this->products_array);

        $this->set_content_data('pager', $this->pager);

        $this->set_content_data(
            'pages',
            $this->getPages(
                $this->pager->page(),
                $this->maxDisplayPageLinks,
                $this->pager->totalPageCount()
            )
        );

        $this->set_content_data('NAVIGATION', $this->navigation_html);
        $this->set_content_data('bar', $this->navigation_html);
        $this->set_content_data('NAVIGATION_INFO', $this->navigation_info_html);
        $this->set_content_data('NAVIGATION_PAGES_COUNT', $this->navigation_pages_count);
        $this->set_content_data('info', $this->navigation_info_html);

        $navigationUrl = splitPageResults::get_navigation_url();
        $this->set_content_data('navigation_url', $navigationUrl);
        $this->set_content_data('page_param', strpos($navigationUrl, '?') !== false ? '&page=' : '?page=');

        if (isset($this->search_keywords)) {
            $this->set_content_data('SEARCH_RESULT_PAGE', 1);
            $this->set_content_data('KEYWORDS', gm_prepare_string($this->search_keywords, true));
        }

        if ($this->listing_sort !== null) {
            $this->set_content_data('SORT', htmlspecialchars_wrapper($this->listing_sort));
        }

        $this->set_content_data(
            'SORTING_FORM_ACTION_URL',
            htmlspecialchars_wrapper($this->sorting_form_action_url)
        );
        $this->set_content_data('VIEW_MODE', $this->view_mode);
        $this->set_content_data('VIEW_MODE_URL_DEFAULT', $this->view_mode_url_default);
        $this->set_content_data('VIEW_MODE_URL_TILED', $this->view_mode_url_tiled);
        $this->set_content_data('showManufacturerImages', $this->showManufacturerImages);
        $this->set_content_data('showProductRibbons', $this->showProductRibbons);
        $this->set_content_data('showRating', $this->showRating);
        $this->set_content_data(
            'SHOW_PRODUCTS_MODEL',
            gm_get_conf('SHOW_PRODUCTS_MODEL_IN_PRODUCT_LISTS') === 'true'
        );

        $this->add_cache_id_elements($this->cache_id_parameter_array);
    }
}
