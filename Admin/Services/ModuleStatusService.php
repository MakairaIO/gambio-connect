<?php

namespace GXModules\Makaira\GambioConnect\Admin\Services;

// TODO: Maybe we need to add bypass or stripe checkoutsession here to

class ModuleStatusService
{
    public function __construct(
        protected ModuleConfigService $moduleConfig
    ) {
    }

    public function isInstalled(): bool
    {
        return $this->moduleConfig->getIsInstalled();
    }

    public function isInSetup(): bool
    {
        return $this->isInstalled()
            && $this->moduleConfig->getStatus() === "inProgress";
    }

    public function isSetUp(): bool
    {
        return $this->isInstalled()
            && $this->makairaConfigIsSet()
            && $this->moduleConfig->getStatus() === "setup";
    }

    public function isActive(): bool
    {
        return $this->isSetUp()
            && $this->moduleConfig->getIsActive();
    }

    private function makairaConfigIsSet(): bool
    {
        $urlSet = !empty($this->moduleConfig->getMakairaUrl());
        $instanceSet = !empty($this->moduleConfig->getMakairaInstance());
        $secretSet = !empty($this->moduleConfig->getMakairaSecret());

        return $urlSet && $instanceSet && $secretSet;
    }
}
