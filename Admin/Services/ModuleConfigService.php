<?php

namespace GXModules\Makaira\GambioConnect\Admin\Services;

use Gambio\Core\Configuration\Services\ConfigurationService;

class ModuleConfigService
{

  private const CONFIG_PREFIX = 'modules/MakairaGambioConnect/';

  public function __construct(private ConfigurationService $configurationService)
  {
  }

  public function getMakairaUrl(): string
  {
    return $this->getConfigValue('makairaUrl');
  }

  public function setMakairaUrl(string $value): self
  {
    $this->setConfigValue('makairaUrl', $value);
    return $this;
  }

  public function getMakairaInstance(): string
  {
    return $this->getConfigValue('makairaInstance');
  }

  public function setMakairaInstance(string $value): self
  {
    $this->setConfigValue('makairaInstance', $value);
    return $this;
  }

  public function getMakairaSecret(): string
  {
    return $this->getConfigValue('makairaSecret');
  }

  public function setMakairaSecret(string $value): self
  {
    $this->setConfigValue('makairaSecret', $value);
    return $this;
  }

  public function getIsActive(): bool
  {
    return (bool) $this->getConfigValue('active');
  }

  public function setIsActive(bool $value): self
  {
    $this->setConfigValue('active', (string) $value);
    return $this;
  }

  public function getStatus(): string
  {
    return $this->getConfigValue('status');
  }

  public function setStatus(string $value): self
  {
    $this->setConfigValue('status', $value);
    return $this;
  }


  public function getIsInstalled(): bool
  {
    return (bool) $this->configurationService->find('gm_configuration/MODULE_CENTER_GAMBIOCONNECT_INSTALLED')?->value();
  }


  private function getConfigValue(string $key): string
  {
    return $this->configurationService->find(self::CONFIG_PREFIX . $key)?->value() ?? '';
  }

  private function setConfigValue(string $key, string $value): void
  {
    $this->configurationService->save(self::CONFIG_PREFIX . $key, $value);
  }
}
