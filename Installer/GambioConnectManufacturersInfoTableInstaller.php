<?php

namespace GXModules\Makaira\GambioConnect\Installer;

use Doctrine\DBAL\Connection;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectTableInstallerInterface;

class GambioConnectManufacturersInfoTableInstaller implements GambioConnectTableInstallerInterface
{

    public static function install(Connection $connection): void
    {
        $connection->executeStatement("
        CREATE TRIGGER makaira_connect_manufacturers_info_create_trigger AFTER INSERT on manufacturers_info
        FOR EACH ROW
        CALL makairaChange(NEW.manufacturers_id, 'manufacturer')
        ");
        
        $connection->executeStatement("
        CREATE TRIGGER makaira_connect_manufacturers_info_update_trigger AFTER INSERT on manufacturers_info
        FOR EACH ROW
        CALL makairaChange(NEW.manufacturers_id, 'manufacturer')
        ");
        
        $connection->executeStatement("
        CREATE TRIGGER makaira_connect_manufacturers_info_delete_trigger AFTER DELETE on manufacturers_info
        FOR EACH ROW
        CALL makairaChange(OLD.manufacturers_id, 'manufacturer')
        ");
    }

    public static function uninstall(Connection $connection): void
    {
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_info_create_trigger");
        
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_info_update_trigger");
        
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_info_delete_trigger");
    }
}