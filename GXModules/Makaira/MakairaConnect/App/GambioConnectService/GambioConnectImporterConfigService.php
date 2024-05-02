<?php

namespace GXModules\Makaira\MakairaConnect\App\GambioConnectService;

use GXModules\Makaira\MakairaConnect\Admin\Actions\App\GambioConnectService;

class GambioConnectImporterConfigService extends GambioConnectService
{
    public function setUpImporter(): void
    {
        $this->client->createImporter();
    }
}
