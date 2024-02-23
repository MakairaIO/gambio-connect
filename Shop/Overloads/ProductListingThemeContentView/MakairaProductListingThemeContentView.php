<?php

class MakairaProductListingThemeContentView extends ProductListingThemeContentView
{
    private \GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService $moduleConfigService;

    private \GXModules\Makaira\GambioConnect\App\MakairaClient $makairaClient;

    public function __construct($p_template = 'default')
    {
        parent::__construct($p_template);

        $configurationService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationService::class);

        $this->moduleConfigService = new \GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService($configurationService);

        $this->makairaClient = new \GXModules\Makaira\GambioConnect\App\MakairaClient($configurationService);
    }

    public function prepare_data()
    {
        parent::prepare_data();
    }
}
