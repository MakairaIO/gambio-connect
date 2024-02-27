<?php

class MakairaSearchAutoCompleterThemeContentView extends SearchAutoCompleterThemeContentView
{
    public $v_template_dir = __DIR__ . '/GXModules/Makaira/GambioConnect/Shop/ui/template';

    public $v_caching_enabled = false;

    public function __construct()
    {
        parent::__construct();

        $this->v_content_template = 'autosuggest.html';
    }
}