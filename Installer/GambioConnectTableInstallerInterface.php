<?php

namespace GXModules\Makaira\GambioConnect\Installer;

use Doctrine\DBAL\Connection;

interface GambioConnectTableInstallerInterface
{
    public static function install(Connection $connection): void;
    
    public static function uninstall(Connection $connection): void;
}