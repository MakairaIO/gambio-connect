<?php

/*
  Documentation: https://developers.gambio.de/docs/4.8.0.0/tutorials-gx4/framework/legacy-architecture/#create-a-subdirectory-for-the-overload
*/

// phpcs:ignore
class MakairaSearchBoxThemeContentView extends MakairaSearchBoxThemeContentView_parent
{
    private $configurationStorage;

    private \GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService $moduleConfigService;

    private \GXModules\MakairaIO\MakairaConnect\App\MakairaClient $makairaClient;

    public function __construct()
    {
        parent::__construct();
        $this->configurationStorage = MainFactory::create('GXModuleConfigurationStorage', 'Makaira/MakairaConnect');

        $configurationService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationService::class);

        $this->moduleConfigService = new \GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService($configurationService);

        $this->makairaClient = new \GXModules\MakairaIO\MakairaConnect\App\MakairaClient($configurationService);
    }

    // phpcs:ignore
    public function prepare_data()
    {

        parent::prepare_data();

        if ($this->moduleConfigService->isMakairaImporterSetupDone() || $this->moduleConfigService->isPublicFieldsSetupDone()) {
            $makairaActiveSearch = $this->configurationStorage->get('makairaActiveSearch');
            $jsPublicPath =
                DIR_WS_CATALOG
                .'GXModules/Makaira/MakairaConnect/Shop/ui/assets/makaira-search.js?'
                .$_SERVER['REQUEST_TIME'];
            $cssPublicPath =
                DIR_WS_CATALOG
                .'GXModules/Makaira/MakairaConnect/Shop/ui/assets/makaira-search.css?'
                .$_SERVER['REQUEST_TIME'];

            $this->content_array['MAKAIRA_ACTIVE_SEARCH'] = $makairaActiveSearch;
            $this->content_array['makaira_search_js_path'] = $jsPublicPath;
            $this->content_array['makaira_search_css_path'] = $cssPublicPath;
            $this->content_array['FORM_ACTION_URL'] = 'advanced_search_result.php';
        }
    }
}
