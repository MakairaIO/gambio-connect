<?php

namespace GXModules\Makaira\GambioConnect\Installer;

use Doctrine\DBAL\Connection;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectTableInstallerInterface;

class GambioConnectProductsToCategoriesTableInstaller implements GambioConnectTableInstallerInterface
{
    public static function install(Connection $connection): void
    {
        $connection->executeStatement("
            CREATE TRIGGER makaira_connect_products_to_categories_create_trigger_product AFTER INSERT on products_to_categories
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product')
        ");

        $connection->executeStatement("
            CREATE TRIGGER makaira_connect_products_to_categories_create_trigger_category AFTER INSERT on products_to_categories
            FOR EACH ROW
            CALL makairaChange(NEW.categories_id, 'category')
        ");

        $connection->executeStatement("
            CREATE TRIGGER makaira_connect_products_to_categories_update_trigger_product AFTER UPDATE on products_to_categories
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product')
        ");

        $connection->executeStatement("
            CREATE TRIGGER makaira_connect_products_to_categories_update_trigger_category AFTER UPDATE on products_to_categories
            FOR EACH ROW
            CALL makairaChange(NEW.categories_id, 'category')
        ");

        $connection->executeStatement("
            CREATE TRIGGER makaira_connect_products_to_categories_delete_trigger_product AFTER DELETE on products_to_categories
            FOR EACH ROW
            CALL makairaChange(OLD.products_id, 'product')
        ");

        $connection->executeStatement("
            CREATE TRIGGER makaira_connect_products_to_categories_delete_trigger_category AFTER DELETE on products_to_categories
            FOR EACH ROW
            CALL makairaChange(OLD.categories_id, 'category')
        ");
    }

    public static function uninstall(Connection $connection): void
    {
        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_create_trigger_product");

        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_create_trigger_category");

        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_update_trigger_product");

        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_update_trigger_category");

        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_delete_trigger_product");

        $connection->executeStatement("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_delete_trigger_category");
    }
}
