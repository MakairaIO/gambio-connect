<?php

class MakairaLiveSearchAjaxHandler extends LiveSearchAjaxHandler
{
    private \GXModules\MakairaIO\MakairaConnect\App\Core\MakairaRequest $makairaRequest;

    public function proceed()
    {
        if (defined('_GM_VALID_CALL') === false) {
            die('x0');
        }

        $configurationService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationService::class);

        $moduleConfigService = new \GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService($configurationService);

        $this->makairaRequest = new \GXModules\MakairaIO\MakairaConnect\App\Core\MakairaRequest($moduleConfigService->getMakairaUrl(), $moduleConfigService->getMakairaInstance(), $_SESSION['language_code'], $moduleConfigService->getMakairaSecret());

        $keywords = trim($this->v_data_array['GET']['needle']);

        $result = $this->makairaRequest->fetchAutoSuggest($keywords);

        $moduleContent = [
            'products' => $result['product']['items'] ?? [],
            'categories' => $result['category']['items'] ?? [],
            'manufacturers' => $result['manufacturer']['items'] ?? [],
            'links' => $result['links']['items'] ?? [],
            'pages' => $result['pages']['items'] ?? []
        ];

        $view = MainFactory::create('SearchAutoCompleterThemeContentView');

        $view->set_content_data('result', $moduleContent);

        $view->set_template_dir(__DIR__ . '/../../ui/template');

        $view->set_content_template('autosuggest.html');

        $this->v_output_buffer = $view->get_html();

        return $this->v_output_buffer;
    }
}
