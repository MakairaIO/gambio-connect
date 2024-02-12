<?php

use GXModules\Makaira\GambioConnect\GambioConnectInstaller;

class GambioConnectModuleCenterModule extends AbstractModuleCenterModule
{

    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('title', 'general');
        $this->description = $this->languageTextManager->get_text('description', 'general');
        $this->sortOrder   = 1;
    }

    public function install()
    {
        parent::install();
        GambioConnectInstaller::onInstallation($this->db);
    }


    public function uninstall()
    {
        parent::uninstall();
        GambioConnectInstaller::onUninstallation($this->db);
    }
}
