<?php

namespace GXModules\Makaira\GambioConnect\Installer;

use Doctrine\DBAL\Connection;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectTableInstallerInterface;

class GambioConnectManufacturersTableInstaller implements GambioConnectTableInstallerInterface
{

    public static function install(Connection $connection): void
    {
        $connection->executeStatement("
        CREATE TRIGGER makaira_connect_manufacturers_create_trigger AFTER INSERT on manufacturers
        FOR EACH ROW
        CALL makairaChange(NEW.manufacturers_id, 'manufacturer')
        ");
        
        $connection->executeStatement("
        CREATE TRIGGER makaira_connect_manufacturers_update_trigger AFTER UPDATE on manufacturers
        FOR EACH ROW
        CALL makairaChange(NEW.manufacturers_id, 'manufacturer')
        ");
        
        $connection->executeStatement("
        CREATE TRIGGER makaira_connect_manufacturers_delete_trigger AFTER DELETE on manufacturers
        FOR EACH ROW
        CALL makairaChange(OLD.manufacturers_id, 'manufacturer')
        ");
    }

    public static function uninstall(Connection $connection): void
    {
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_create_trigger");
        
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_update_trigger");
        
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_delete_trigger");
    }
}