<?php

namespace GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;

use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;

class GambioConnectImporterConfigService extends GambioConnectService
{
    public function setUpImporter(): void
    {
        $this->client->createImporter();
    }
}
