<?php

namespace GXModules\Makaira\MakairaConnect\Admin\Services;

use Gambio\Core\Configuration\Services\ConfigurationService;

class ModuleConfigService
{
    private const CONFIG_PREFIX = 'modules/MakairaGambioConnect/';

    public const CONFIG_MAKAIRA_URL = 'makairaUrl';

    public const CONFIG_MAKAIRA_INSTANCE = 'makairaInstance';

    public const CONFIG_MAKAIRA_SECRET = 'makairaSecret';

    public const CONFIG_MAKAIRA_ACTIVE = 'active';

    public const CONFIG_MAKAIRA_STATUS = 'status';

    public const CONFIG_MAKAIRA_PUBLICFIELDS_SETUP_DONE = 'publicFieldsSetupDone';

    public const CONFIG_MAKAIRA_IMPORTER_SETUP_DONE = 'makairaImporterSetupDone';

    public const CONFIG_MAKAIRA_INSTALLED = 'gm_configuration/MODULE_CENTER_GAMBIOCONNECT_INSTALLED';

    public const CONFIG_MAKAIRA_ACTIVE_SEARCH = 'makairaActiveSearch';

    public const CONFIG_MAKAIRA_CRONJOB_ACTIVE = 'cronjobs/MakairaGambioConnect/active';

    public const CONFIG_MAKAIRA_CRONJOB_INTERVAL = 'cronjobs/MakairaGambioConnect/interval';

    public const CONFIG_MAKAIRA_RECO_CROSS_SELLING = 'recoCrossSelling';

    public const CONFIG_MAKAIRA_RECO_REVERSE_CROSS_SELLING = 'recoReverseCrossSelling';

    public const CONFIG_MAKAIRA_INSTALLATION_SERVICE_CALLED = 'makairaInstallationServiceCalled';

    public const CONFIG_MAKAIRA_INSTALLATION_SERVICE_REQUEST_DATA = 'makairaInstallationServiceRequestData';

    public const CONFIG_MAKAIRA_INSTALLATION_SERVICE_CALL_COUNTER = 'makairaInstallationServiceCallCounter';

    public function __construct(private ConfigurationService $configurationService)
    {
    }

    public function getMakairaUrl(): string
    {
        return $this->getConfigValue(self::CONFIG_MAKAIRA_URL);
    }

    public function setMakairaUrl(string $value): self
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_URL, $value);
        return $this;
    }

    public function getMakairaInstance(): string
    {
        return $this->getConfigValue(self::CONFIG_MAKAIRA_INSTANCE);
    }

    public function setMakairaInstance(string $value): self
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_INSTANCE, $value);
        return $this;
    }

    public function getMakairaSecret(): string
    {
        return $this->getConfigValue(self::CONFIG_MAKAIRA_SECRET);
    }

    public function setMakairaSecret(string $value): self
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_SECRET, $value);
        return $this;
    }

    public function getStatus(): string
    {
        return $this->getConfigValue(self::CONFIG_MAKAIRA_STATUS);
    }

    public function setStatus(string $value): self
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_STATUS, $value);
        return $this;
    }


    public function getIsInstalled(): bool
    {
        return (bool)$this->configurationService->find(self::CONFIG_MAKAIRA_INSTALLED)?->value();
    }

    public function isPublicFieldsSetupDone(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_MAKAIRA_PUBLICFIELDS_SETUP_DONE);
    }

    public function setPublicFieldsSetupDone(): void
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_PUBLICFIELDS_SETUP_DONE, true);
    }

    public function getRecoCrossSelling(): string
    {
        return $this->getConfigValue(self::CONFIG_MAKAIRA_RECO_CROSS_SELLING);
    }

    public function setRecoCrossSelling(string $recoCrossSelling = ''): void
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_RECO_CROSS_SELLING, $recoCrossSelling);
    }

    public function getRecoReverseCrossSelling(): string
    {
        return $this->getConfigValue(self::CONFIG_MAKAIRA_RECO_REVERSE_CROSS_SELLING);
    }

    public function setRecoReverseCrossSelling(string $recoReverseCrossSelling = ''): void
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_RECO_REVERSE_CROSS_SELLING, $recoReverseCrossSelling);
    }

    public function getCronjobStatus(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_MAKAIRA_CRONJOB_ACTIVE);
    }

    public function setMakairaImporterSetupDone(): void
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_IMPORTER_SETUP_DONE, true);
    }

    public function isMakairaImporterSetupDone(): bool
    {
        return (bool) $this->getConfigValue(self::CONFIG_MAKAIRA_IMPORTER_SETUP_DONE);
    }

    public function setMakairaCronJobActive(): void
    {
        $this->configurationService->save(self::CONFIG_MAKAIRA_CRONJOB_ACTIVE, true);
    }

    public function setMakairaCronJobInterval(): void
    {
        $this->configurationService->save(self::CONFIG_MAKAIRA_CRONJOB_INTERVAL, '*/1 * * * *');
    }

    private function getConfigValue(string $key): string
    {
        return $this->configurationService->find(self::CONFIG_PREFIX . $key)?->value() ?? '';
    }

    private function setConfigValue(string $key, string $value): void
    {
        $this->configurationService->save(self::CONFIG_PREFIX . $key, $value);
    }

    public static function getModuleConfigKeys(): array
    {
        return [
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_ACTIVE,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_URL,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_INSTANCE,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_SECRET,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_ACTIVE_SEARCH,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_PUBLICFIELDS_SETUP_DONE,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_IMPORTER_SETUP_DONE,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_INSTALLATION_SERVICE_REQUEST_DATA,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_INSTALLATION_SERVICE_CALL_COUNTER,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_INSTALLATION_SERVICE_CALLED,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_RECO_CROSS_SELLING,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_RECO_REVERSE_CROSS_SELLING,
            self::CONFIG_MAKAIRA_CRONJOB_ACTIVE,
            self::CONFIG_MAKAIRA_CRONJOB_INTERVAL
        ];
    }
}
