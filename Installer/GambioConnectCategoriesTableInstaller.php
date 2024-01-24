<?php

namespace GXModules\Makaira\GambioConnect\Installer;

use Doctrine\DBAL\Connection;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectTableInstallerInterface;

class GambioConnectCategoriesTableInstaller implements GambioConnectTableInstallerInterface
{

    public static function install(Connection $connection): void
    {
        $connection->executeStatement("
        CREATE TRIGGER makaira_connect_categories_create_trigger AFTER INSERT on categories
        FOR EACH ROW
        CALL makairaChange(NEW.categories_id, 'category')
        ");
        
        $connection->executeStatement("
        CREATE TRIGGER makaira_connect_categories_update_trigger AFTER UPDATE on categories
        FOR EACH ROW
        CALL makairaChange(NEW.categories_id, 'category')
        ");
        
        $connection->executeStatement("
        CREATE TRIGGER makaira_connect_categories_delete_trigger AFTER DELETE on categories
        FOR EACH ROW
        CALL makairaChange(OLD.categories_id, 'category')
        ");
    }

    public static function uninstall(Connection $connection): void
    {
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_categories_create_trigger");
        
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_categories_update_trigger");
        
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_categories_delete_trigger");
    }
}