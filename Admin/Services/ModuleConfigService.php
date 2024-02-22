<?php

namespace GXModules\Makaira\GambioConnect\Admin\Services;

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

    public const CONFIG_MAKAIRA_STRIPE_CHECKOUT_SESSION = 'stripeCheckoutSession';

    public const CONFIG_MAKAIRA_STRIPE_CHECKOUT_EMAIL = 'stripeCheckoutEmail';

    public const CONFIG_MAKAIRA_STRIPE_OVERRIDE = 'stripeOverride';

    public const CONFIG_MAKAIRA_CRONJOB_ACTIVE = 'cronjobs/GambioConnect/active';

    public const CONFIG_MAKAIRA_CRONJOB_INTERVAL = 'cronjobs/GambioConnect/interval';

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

    public function getStripeCheckoutId(): string|null
    {
        return $this->getConfigValue(self::CONFIG_MAKAIRA_STRIPE_CHECKOUT_SESSION);
    }

    public function setStripeCheckoutId(string|null $checkoutId = null): void
    {
        if (!$checkoutId) {
            $this->configurationService->delete(self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_STRIPE_CHECKOUT_SESSION);
        } else {
            $this->setConfigValue(self::CONFIG_MAKAIRA_STRIPE_CHECKOUT_SESSION, $checkoutId);
        }
    }

    public function getStripeCheckoutEmail(): string|null
    {
        return $this->getConfigValue(self::CONFIG_MAKAIRA_STRIPE_CHECKOUT_EMAIL);
    }

    public function setStripeCheckoutEmail(string $email): void
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_STRIPE_CHECKOUT_EMAIL, $email);
    }

    public function isStripeOverrideActive(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_MAKAIRA_STRIPE_OVERRIDE);
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

    public function isMakairaInstallationServiceCalled(): bool
    {
        return (bool) $this->getConfigValue(self::CONFIG_MAKAIRA_INSTALLATION_SERVICE_CALLED);
    }

    public function setMakairaInstallationServiceCalled(): void
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_INSTALLATION_SERVICE_CALLED, true);

        $this->makairaInstallationServiceCalled();
    }

    public function setMakairaInstallationServiceRequestData(array $data): void
    {
        $this->setConfigValue(self::CONFIG_MAKAIRA_INSTALLATION_SERVICE_REQUEST_DATA, json_encode($data));
    }

    public function getMakairaInstallationServiceRequestData(): array
    {
        $data = $this->getConfigValue(self::CONFIG_MAKAIRA_INSTALLATION_SERVICE_REQUEST_DATA);
        if (!empty($data)) {
            return (array)json_decode($data);
        }
        return [];
    }

    public function makairaInstallationServiceCalled(): void
    {
        $counter = $this->getConfigValue(self::CONFIG_MAKAIRA_INSTALLATION_SERVICE_CALL_COUNTER) ?? 0;
        $counter++;
        $this->setConfigValue(self::CONFIG_MAKAIRA_INSTALLATION_SERVICE_CALL_COUNTER, $counter);
    }

    public function shouldMakairaInstallationServiceBeCalled(): bool
    {
        $counter = $this->getConfigValue(self::CONFIG_MAKAIRA_INSTALLATION_SERVICE_CALL_COUNTER) ?? 0;
        return $counter < 2;
    }

    public function setMakairaCronJobActive(): void
    {
        $this->configurationService->save(self::CONFIG_MAKAIRA_CRONJOB_ACTIVE, true);
    }

    public function setMakairaCronJobInterval(): void
    {
        $this->configurationService->save(self::CONFIG_MAKAIRA_CRONJOB_INTERVAL, '*/4 * * * *');
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
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_STRIPE_CHECKOUT_SESSION,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_STRIPE_CHECKOUT_EMAIL,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_STRIPE_OVERRIDE,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_PUBLICFIELDS_SETUP_DONE,
            self::CONFIG_PREFIX . self::CONFIG_MAKAIRA_IMPORTER_SETUP_DONE,
            self::CONFIG_MAKAIRA_CRONJOB_ACTIVE,
            self::CONFIG_MAKAIRA_CRONJOB_INTERVAL
        ];
    }
}
