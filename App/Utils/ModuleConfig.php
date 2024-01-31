<?php

namespace GXModules\Makaira\GambioConnect\App\Utils;

use Gambio\Core\Configuration\App\ConfigurationFinder;

class ModuleConfig
{

  public function __construct(private ConfigurationFinder $configurationFinder)
  {
    
  }

  public function makairaUrl() {
    return $this->configurationFinder->get('modules/MakairaGambioConnect/makairaUrl', null);
  }

  public function makairaInstance() {
    return $this->configurationFinder->get('modules/MakairaGambioConnect/makairaInstance', null);
  }

  public function makairaSecret() {
    return $this->configurationFinder->get('modules/MakairaGambioConnect/makairaSecret', null);
  }

  public function isActiveSearch() {
    return $this->configurationFinder->get('modules/MakairaGambioConnect/active_search', false);
  }
}
