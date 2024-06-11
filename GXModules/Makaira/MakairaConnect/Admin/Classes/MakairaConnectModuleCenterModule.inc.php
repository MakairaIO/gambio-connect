<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use GXModules\Makaira\MakairaConnect\MakairaConnectInstaller;

class MakairaConnectModuleCenterModule extends AbstractModuleCenterModule
{
    // phpcs:ignore
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('title', 'makaira_connect_general');
        $this->description = $this->languageTextManager->get_text('description', 'makaira_connect_general');
        $this->sortOrder   = 1;
    }

    public function install()
    {
        parent::install();
        MakairaConnectInstaller::onInstallation($this->db);
    }


    public function uninstall()
    {
        parent::uninstall();
        MakairaConnectInstaller::onUninstallation($this->db);
    }
}
