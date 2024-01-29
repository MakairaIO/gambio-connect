<?php

namespace GXModules\Makaira\GambioConnect\Installer;

use Doctrine\DBAL\Connection;

class GambioConnectProductsTableInstaller
{
    public static function install(Connection $connection): void
    {
        $connection->executeStatement("
            CREATE TRIGGER makaira_connect_product_create_trigger AFTER INSERT ON products
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product');
        ");
        
        $connection->executeStatement("
            CREATE TRIGGER makaira_connect_product_update_trigger AFTER UPDATE ON products
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product');
        ");
        
        $connection->executeStatement("
            CREATE TRIGGER makaira_connect_product_delete_trigger AFTER DELETE on products
            FOR EACH ROW
            CALL makairaChange(OLD.products_id, 'product');
        ");
    }
    
    public static function uninstall(Connection $connection): void {
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_product_create_trigger");
        
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_product_update_trigger");
        
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_product_delete_trigger");
    }
}