<?php

class GambioConnectModuleCenterModule extends AbstractModuleCenterModule
{
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('sample_title');
        $this->description = $this->languageTextManager->get_text('sample_description');
    }


    /**
     * Install module and set own install flag in module table
     */
    public function install()
    {
        parent::install();


        // todo
        //$changeService->createTable();


    }


    /**
     * Uninstall module and set own install flag in module table
     */
    public function uninstall()
    {
        parent::uninstall();

        //$changeService->dropTable();


    }
}
