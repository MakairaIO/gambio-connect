<?php

require __DIR__ . '/../SplitPageResults/makaira_split_page_results.php';

class MakairaProductListingContentControl extends MakairaProductListingContentControl_parent
{
    private \GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService $moduleConfigService;

    private \GXModules\MakairaIO\MakairaConnect\App\MakairaClient $makairaClient;

    private $products = [];

    private $totalProducts = 0;

    private $category;

    private string $groupBy = '';

    public function __construct()
    {
        parent::__construct();

        $configurationService = LegacyDependencyContainer::getInstance()->get(
            \Gambio\Core\Configuration\Services\ConfigurationService::class
        );

        $this->moduleConfigService = new \GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService(
            $configurationService
        );

        $this->makairaClient = new \GXModules\MakairaIO\MakairaConnect\App\MakairaClient($configurationService);

        $this->product_listing_view = MainFactory::create('MakairaProductListingThemeContentView');
    }

    private function isSearch(): bool
    {
        return (bool) $this->search_keywords;
    }

    private function getCategory($categoryId)
    {
        $this->groupBy = $_SESSION['languages_id'].'_'.$this->currency_code.'_'.$this->customers_status_id;
        if ($this->isSearch()) {
            $result = $this->makairaClient->search(
                $this->search_keywords,
                $this->determine_max_display_search_results(),
                $this->page_number ?? 0,
                $this->prepareSortingForMakaira(),
                $this->groupBy
            );

            $this->products = array_map(function ($makairaProduct) {
                return $makairaProduct['fields'];
            }, $result['product']['items']);
        } elseif (empty($categoryId)) {
            $this->products = [];

            $this->totalProducts = 0;
        } else {
            $result = $this->makairaClient->getProducts(
                $categoryId,
                $this->determine_max_display_search_results(),
                $this->page_number ?? 0,
                $this->prepareSortingForMakaira(),
                $this->groupBy
            );

            $banners = $result['banners'];

            foreach ($result['product']['items'] as $index => $product) {
                foreach ($banners as $bannerIndex => $banner) {
                    if ((int) $banner['position'] - 1 === $index) {
                        $this->products[] = [
                            'id' => $banner['id'],
                            'fields' => [
                                'title' => $banner['title'],
                                'shortdesc' => $banner['description'],
                                'longdesc' => $banner['description'],
                                'url' => $banner['link'],
                                'active' => true,
                            ],
                        ];
                        unset($banners[$bannerIndex]);
                    }
                }
                $this->products[] = (array) $product['fields'];
            }

            $this->totalProducts = $result['product']['total'];
        }
    }

    public function proceed($p_action = 'default')
    {
        if ($this->moduleConfigService->isPublicFieldsSetupDone(
        ) && $this->moduleConfigService->isMakairaImporterSetupDone()) {
            try {
                $this->getCategory($this->categories_id);

                $t_html_output = '';

                $t_uninitialized_array = $this->get_uninitialized_variables([
                    'coo_product',
                    'current_category_id',
                    'current_page',
                    'customers_status_id',
                    'languages_id',
                ]);

                if (empty($t_uninitialized_array)) {
                    switch ($p_action) {
                        case 'search_result':
                            $this->init_feature_filter();

                            // get feature_value_groups from FilterManager
                            $t_feature_value_group_array = $this->coo_filter_manager->get_feature_value_group_array();
                            $coo_filter_selection_content_view = MainFactory::create_object('FilterSelectionThemeContentView');
                            $coo_filter_selection_content_view->set_('feature_value_group_array', $t_feature_value_group_array);
                            $coo_filter_selection_content_view->set_('language_id', $_SESSION['languages_id']);
                            $this->filter_selection_html = $coo_filter_selection_content_view->get_html();

                            $this->build_search_result_sql();

                            break;
                        default:

                            if (xtc_check_categories_status($this->current_category_id) >= 1) {
                                $this->v_output_buffer = $this->get_error_html_output(CATEGORIE_NOT_FOUND);
                                $this->empty_result = true;

                                return true;
                            }

                            $this->init_feature_filter();
                            $t_category_depth = $this->determine_category_depth();

                            switch ($t_category_depth) {
                                case 'top':
                                    // start page
                                    $this->v_output_buffer = $this->get_start_page_html_output();

                                    return true;

                                    break;
                                case 'nested':
                                    $t_html_output = $this->get_category_listing_html_output();

                                    // no break;
                                default:
                                    $this->build_sql_query();
                            }
                    }

                    $this->extend_proceed($p_action);

                    if (empty($this->sql_query)) {
                        return true;
                    }

                    $t_max_display_search_results = $this->determine_max_display_search_results();

                    // save last listing query for ProductNavigator ($_SESSION['last_listing_sql'])
                    $this->last_listing_sql = $this->sql_query;

                    $coo_listing_split = new MakairaSplitPageResults($this->products, $this->page_number,
                        $t_max_display_search_results, 'p.products_id');
                    $t_products_array = [];

                    if ($coo_listing_split->number_of_rows > 0) {
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

                        $coo_navigation_view = MainFactory::create_object(SplitNavigationContentView::class);
                        $coo_navigation_view->set_('coo_split_page_results', $coo_listing_split);
                        $t_navigation_html = $coo_navigation_view->get_html();

                        $t_rows_count = 0;
                        $t_query = $coo_listing_split->sql_query;
                        //$t_result = xtc_db_query($t_query);

                        foreach ($this->products as $t_product_array) {
                            $t_rows_count++;

                            // check if product has properties
                            $t_query = 'SELECT COUNT(*) AS `count` FROM `products_properties_combis` WHERE `products_id` = '
                                .$t_product_array['id'];
                            $t_combis_result = xtc_db_query($t_query);
                            $t_count_combis_array = xtc_db_fetch_array($t_combis_result);

                            if ($t_count_combis_array['count'] > 0) {
                                $t_product_has_properties = true;
                            } else {
                                $t_product_has_properties = false;
                            }

                            $GLOBALS['xtPrice']->showFrom_Attributes = true;
                            if (((gm_get_conf('MAIN_SHOW_ATTRIBUTES') == 'true'
                                        && isset($t_category_data_array['gm_show_attributes']) == false)
                                    || (isset($t_category_data_array['gm_show_attributes'])
                                        && $t_category_data_array['gm_show_attributes'] == '1'))
                                && $t_product_has_properties == false) {
                                $GLOBALS['xtPrice']->showFrom_Attributes = false;
                            }

                            $coo_product = $t_product_array['coo_product'];
                            $t_products_array[] = $coo_product;

                            $t_attributes_html = '';
                            if (((gm_get_conf('MAIN_SHOW_ATTRIBUTES') == 'true'
                                        && isset($t_category_data_array['gm_show_attributes']) == false)
                                    || (isset($t_category_data_array['gm_show_attributes'])
                                        && $t_category_data_array['gm_show_attributes'] == '1'))
                                && $t_product_has_properties == false) {
                                // CREATE ProductAttributesContentView OBJECT
                                $coo_product_attributes = MainFactory::create_object('ProductAttributesThemeContentView');

                                // SET TEMPLATE
                                $t_filepath = DIR_FS_CATALOG.StaticGXCoreLoader::getThemeControl()
                                    ->getGmProductOptionsTemplatePath();

                                $c_template = $coo_product_attributes->get_default_template($t_filepath,
                                    'product_listing_option_template_', $coo_product->data['gm_options_template']);

                                $coo_product_attributes->set_gm_product_option_template($c_template);

                                // SET DATA
                                $coo_product_attributes->set_coo_product($coo_product);
                                $coo_product_attributes->set_language_id($_SESSION['languages_id']);

                                // GET HTML
                                $t_attributes_html = $coo_product_attributes->get_html();
                            }

                            $t_graduated_prices_html = '';
                            if ($t_category_data_array['show_graduated_prices']) {
                                $coo_graduated_prices = MainFactory::create_object('GraduatedPricesThemeContentView');
                                $coo_graduated_prices->set_coo_product($coo_product);
                                $coo_graduated_prices->set_customers_status_graduated_prices($_SESSION['customers_status']['customers_status_graduated_prices']);
                                $coo_graduated_prices->set_gm_graduated_price_template();
                                $t_graduated_prices_html = $coo_graduated_prices->get_html();
                            }
                            if (xtc_has_product_attributes($t_product_array['id'])) {
                                $gm_has_attributes = 1;
                            } else {
                                $gm_has_attributes = 0;
                            }

                            $t_products_array[count($t_products_array)
                            - 1] = array_merge($t_products_array[count($t_products_array) - 1], [
                                'GM_ATTRIBUTES' => $t_attributes_html,
                                'GM_GRADUATED_PRICES' => $t_graduated_prices_html,
                                'GM_HAS_ATTRIBUTES' => $gm_has_attributes,
                            ]);

                            if (empty($coo_product->data['quantity_unit_id']) == false
                                && (! $t_product_has_properties
                                    || ($t_product_has_properties
                                        && $coo_product->data['use_properties_combis_quantity'] == '0'))
                            ) {
                                $t_products_array[count($t_products_array)
                                - 1] = array_merge($t_products_array[count($t_products_array) - 1],
                                    ['UNIT' => $coo_product->data['unit_name']]);
                            }

                            if ($t_category_show_quantity) {
                                if (empty($coo_product->data['gm_show_qty_info']) == false
                                    && (! $t_product_has_properties
                                        || ($t_product_has_properties
                                            && ($coo_product->data['use_properties_combis_quantity'] === '0'
                                                || $coo_product->data['use_properties_combis_quantity'] === '1')))
                                ) {
                                    $t_products_array[count($t_products_array)
                                    - 1] = array_merge($t_products_array[count($t_products_array) - 1],
                                        ['PRODUCTS_QUANTITY' => $coo_product->data['products_quantity']]);
                                }
                            }

                            if ($t_product_has_properties) {
                                $t_products_array[count($t_products_array) - 1]['SHOW_PRODUCTS_WEIGHT'] = 0;
                                $t_products_array[count($t_products_array) - 1]['GM_HAS_PROPERTIES'] = true;
                            }

                            $t_products_array[count($t_products_array)
                            - 1] = array_merge($t_products_array[count($t_products_array) - 1],
                                ['ABROAD_SHIPPING_INFO_LINK' => main::get_abroad_shipping_info_link()]);

                            unset($products_options_data);
                        }

                        if (isset($t_category_data_array['view_mode_tiled'])) {
                            $t_view_mode = $this->determine_view_mode($t_category_data_array['view_mode_tiled']);
                        } else {
                            $t_view_mode = $this->determine_view_mode();
                        }

                        $this->build_cache_id_parameter_array([$t_view_mode]);

                        if ($this->product_listing_view->get_content_template() === ''
                            || basename($this->product_listing_view->get_content_template())
                            === $this->product_listing_view->get_template_name('default')) {
                            $this->product_listing_view->set_template($t_category_data_array['listing_template'] ?? null);
                        }

                        $this->product_listing_view->set_('cache_id_parameter_array', $this->cache_id_parameter_array);
                        $this->product_listing_view->set_('category_id', $this->current_category_id);
                        $this->product_listing_view->set_('c_path', $this->c_path);
                        $this->product_listing_view->set_('category_description', $t_categories_description);
                        $this->product_listing_view->set_('category_description_bottom', $t_categories_description_bottom);
                        $this->product_listing_view->set_('category_heading_title', $t_category_heading_title);
                        $this->product_listing_view->set_('category_image_alt_text', $t_category_image_alt_text);
                        $this->product_listing_view->set_('category_image', $t_category_image);
                        $this->product_listing_view->set_('showCategoriesImageInDescription', $t_show_category_image);
                        $this->product_listing_view->set_('category_name', $t_category_name);
                        $this->product_listing_view->set_('coo_mn_data_container', $this->coo_mn_data_container);

                        if (isset($this->filter_selection_html)) {
                            $this->product_listing_view->set_('filter_selection_html', $this->filter_selection_html);
                        }

                        // De-duplicate multidimensional array (@link http://stackoverflow.com/a/946300)
                        $t_hidden_get_params_array = array_map('unserialize',
                            array_unique(array_map('serialize', $this->build_hidden_get_params_array())));
                        $this->product_listing_view->set_('get_params_hidden_data_array',
                            array_values($t_hidden_get_params_array));

                        if (isset($this->listing_count)) {
                            $this->product_listing_view->set_('listing_count', $this->listing_count);
                        }

                        if (isset($this->listing_sort)) {
                            $this->product_listing_view->set_('listing_sort', $this->listing_sort);
                        }

                        if (isset($this->manufacturers_id)) {
                            $this->product_listing_view->set_('manufacturers_id', $this->manufacturers_id);
                        }

                        if (isset($this->manufacturers_data_array)) {
                            $this->product_listing_view->set_('manufacturers_data_array', $this->manufacturers_data_array);
                        }

                        if (isset($this->manufacturers_dropdown)) {
                            $this->product_listing_view->set_('manufacturers_dropdown', $this->manufacturers_dropdown);
                        }

                        if ((defined('CURRENT_THEME') && ! empty(CURRENT_THEME)) || (defined('PREVIEW_MODE') && PREVIEW_MODE)) {
                            $this->product_listing_view->set_('pager', $this->createPager());
                            $this->product_listing_view->set_('maxDisplayPageLinks', (int) MAX_DISPLAY_PAGE_LINKS);
                            $this->product_listing_view->set_('languageTextManager', MainFactory::create('LanguageTextManager'));
                        }

                        $this->product_listing_view->set_('navigation_html', $t_navigation_html);
                        $this->product_listing_view->set_('navigation_info_html',
                            $coo_listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS));
                        $this->product_listing_view->set_('navigation_pages_count', $coo_listing_split->getNumberOfPages());
                        $this->product_listing_view->set_('products_array', $t_products_array);
                        $this->product_listing_view->set_('products_per_page', (int) MAX_DISPLAY_SEARCH_RESULTS);

                        if (isset($this->search_keywords)) {
                            $this->product_listing_view->set_('search_keywords', $this->search_keywords);
                        }

                        $this->product_listing_view->set_('show_quantity', $t_show_quantity);
                        $this->product_listing_view->set_('thumbnail_width', PRODUCT_IMAGE_THUMBNAIL_WIDTH + 10);

                        $t_page_url_array = explode('?', gm_get_env_info('REQUEST_URI'));

                        $this->product_listing_view->set_('sorting_form_action_url', $t_page_url_array[0]);
                        $this->product_listing_view->set_('view_mode', $t_view_mode);

                        $this->product_listing_view->set_('view_mode_url_default', $this->build_view_mode_url('default'));
                        $this->product_listing_view->set_('view_mode_url_tiled', $this->build_view_mode_url('tiled'));

                        $showRating = false;
                        if (gm_get_conf('ENABLE_RATING') === 'true'
                            && gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true'
                        ) {
                            $showRating = true;
                        }

                        $this->product_listing_view->set_('showRating', $showRating);
                        $this->product_listing_view->set_('showManufacturerImages',
                            gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING'));
                        $this->product_listing_view->set_('showProductRibbons', gm_get_conf('SHOW_PRODUCT_RIBBONS'));

                        if ($this->isFilterListing) {
                            $t_html_output = '';
                        }

                        $t_html_output .= $this->product_listing_view->get_html();
                    } elseif (! defined('GM_CAT_COUNT')
                        || GM_CAT_COUNT == 0
                    ) { // GM_CAT_COUNT > 0: products FALSE, sub-categories TRUE
                        $t_html_output = $this->get_error_html_output(TEXT_PRODUCT_NOT_FOUND);
                        $this->empty_result = true;
                    } else {
                        /** @var \CategoryReadService $categoryReadService */
                        $categoryReadService = StaticGXCoreLoader::getService('CategoryRead');
                        $category = $categoryReadService->getCategoryById(new IdType($this->current_category_id));
                        $languageCode = MainFactory::create('LanguageCode', new StringType($_SESSION['language_code']));
                        $categoryDescriptionBottomContentView = MainFactory::create('CategoryDescriptionBottomThemeContentView');
                        $categoryDescriptionBottomContentView->set_content_data('CATEGORIES_DESCRIPTION_BOTTOM',
                            $category->getDescriptionBottom($languageCode));
                        $t_html_output .= $categoryDescriptionBottomContentView->get_html();
                    }
                } else {
                    trigger_error('Variable(s) '.implode(', ', $t_uninitialized_array).' do(es) not exist in class '
                        .get_class($this).' or is/are null', E_USER_ERROR);
                }

                $this->v_output_buffer = $t_html_output;
            } catch (Exception) {
                parent::proceed($p_action);
            }
        } else {
            return parent::proceed($p_action);
        }
    }

    private function prepareSortingForMakaira(): array
    {
        if (! $this->listing_sort) {
            return [];
        }
        $sort = explode('_', $this->listing_sort);
        switch ($sort[0]) {
            case 'name':
                $type = 'title';
                break;
            case 'price':
                $type = 'price';
                break;
            case 'date':
                return [
                    'products_date_added' => $sort[1],
                    'products_date_available' => $sort[1],
                ];
            case 'shipping':
                $type = 'shipping_number_of_days';
                break;
            default:
                return [];
        }

        return [$type => $sort[1]];
    }
}
