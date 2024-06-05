<?php

namespace GXModules\Makaira\MakairaConnect\App\Installer;

use CI_DB_query_builder;

interface GambioConnectTableInstallerInterface
{
    public static function install(CI_DB_query_builder $db): void;

    public static function uninstall(CI_DB_query_builder $db): void;
}
