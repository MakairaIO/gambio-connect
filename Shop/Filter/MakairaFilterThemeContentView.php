<?php

class MakairaFilterThemeContentView extends ThemeContentView
{
    protected $aggregations;
    protected $basepath;

    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('makaira_filter_base.html');
        $this->set_flat_assigns(true);
    }

    protected function set_validation_rules()
    {
        $this->validation_rules_array['aggregations'] = ['type' => 'array'];
        $this->validation_rules_array['basepath'] = ['type' => 'string'];
    }


    public function prepare_data()
    {
        $this->content_array['aggregations'] = $this->aggregations;
        $this->content_array['basepath'] = $this->basepath;
    }
}
