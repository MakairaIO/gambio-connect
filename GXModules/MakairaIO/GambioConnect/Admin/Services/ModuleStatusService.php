<?php

namespace GXModules\Makaira\GambioConnect\Admin\Services;

// TODO: Maybe we need to add bypass or stripe checkoutsession here to

class ModuleStatusService
{
    public function __construct(
        protected ModuleConfigService $moduleConfigService
    ) {
    }

    public function getModuleConfigService(): ModuleConfigService
    {
        return $this->moduleConfigService;
    }

    public function isInstalled(): bool
    {
        return $this->moduleConfigService->getIsInstalled();
    }

    public function isInSetup(): bool
    {
        return $this->isInstalled()
            && $this->moduleConfigService->getStripeCheckoutEmail()
            && $this->moduleConfigService->getStripeCheckoutId()
            && !$this->makairaConfigIsSet();
    }

    public function isSetUp(): bool
    {
        return $this->isInstalled() && $this->makairaConfigIsSet();
    }

    public function isActive(): bool
    {
        return $this->isSetUp();
    }

    private function makairaConfigIsSet(): bool
    {
        $urlSet = !empty($this->moduleConfigService->getMakairaUrl());
        $instanceSet = !empty($this->moduleConfigService->getMakairaInstance());
        $secretSet = !empty($this->moduleConfigService->getMakairaSecret());

        return $urlSet && $instanceSet && $secretSet;
    }
}
