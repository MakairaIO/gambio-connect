<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use GXModules\Makaira\GambioConnect\App\GambioConnectService;

class GambioConnectImporterConfigService extends GambioConnectService
{
    public function setUpImporter(): void
    {
        $this->client->createImporter();
    }
}