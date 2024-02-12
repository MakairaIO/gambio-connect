<?php

class GambioConnectModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    protected function _init()
    {
        $this->redirectUrl = xtc_href_link('makaira/welcome');
    }
}
