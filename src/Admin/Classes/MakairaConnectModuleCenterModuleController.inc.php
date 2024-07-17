<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
class MakairaConnectModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    // phpcs:ignore
    protected function _init()
    {
        $this->redirectUrl = xtc_href_link('makaira/account');
    }
}
