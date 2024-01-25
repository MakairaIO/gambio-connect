<?php
/* 
  Documentation: https://developers.gambio.de/docs/4.8.0.0/tutorials-gx4/framework/legacy-architecture/#create-a-subdirectory-for-the-overload
*/

class MakairaSearchBoxThemeContentView extends MakairaSearchBoxThemeContentView_parent
{  
    private $configurationStorage;

    function __construct() {
        parent::__construct();
        $this->configurationStorage = MainFactory::create('GXModuleConfigurationStorage', 'Makaira/GambioConnect');
    }

    public function prepare_data()
    {
        parent::prepare_data();

        $makairaActiveSearch = $this->configurationStorage->get('makairaActiveSearch');
        $jsPublicPath  = DIR_WS_CATALOG . 'GXModules/Makaira/GambioConnect/ui/assets/makaira-search.js';
        
        $this->content_array['MAKAIRA_ACTIVE_SEARCH'] = $makairaActiveSearch;
        $this->content_array['makaira_search_js_path'] = $jsPublicPath;
    }
}