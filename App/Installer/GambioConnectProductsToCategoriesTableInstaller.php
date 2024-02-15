<?php

namespace GXModules\Makaira\GambioConnect\App\Installer;

use CI_DB_query_builder;

class GambioConnectProductsToCategoriesTableInstaller implements GambioConnectTableInstallerInterface
{
    public static function install(CI_DB_query_builder $db): void
    {
        $db->query("
            CREATE TRIGGER makaira_connect_products_to_categories_create_trigger_product AFTER INSERT on products_to_categories
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product')
        ");

        $db->query("
            CREATE TRIGGER makaira_connect_products_to_categories_create_trigger_category AFTER INSERT on products_to_categories
            FOR EACH ROW
            CALL makairaChange(NEW.categories_id, 'category')
        ");

        $db->query("
            CREATE TRIGGER makaira_connect_products_to_categories_update_trigger_product AFTER UPDATE on products_to_categories
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product')
        ");

        $db->query("
            CREATE TRIGGER makaira_connect_products_to_categories_update_trigger_category AFTER UPDATE on products_to_categories
            FOR EACH ROW
            CALL makairaChange(NEW.categories_id, 'category')
        ");

        $db->query("
            CREATE TRIGGER makaira_connect_products_to_categories_delete_trigger_product AFTER DELETE on products_to_categories
            FOR EACH ROW
            CALL makairaChange(OLD.products_id, 'product')
        ");

        $db->query("
            CREATE TRIGGER makaira_connect_products_to_categories_delete_trigger_category AFTER DELETE on products_to_categories
            FOR EACH ROW
            CALL makairaChange(OLD.categories_id, 'category')
        ");
    }

    public static function uninstall(CI_DB_query_builder $db): void
    {
        $db->query("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_create_trigger_product");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_create_trigger_category");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_update_trigger_product");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_update_trigger_category");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_delete_trigger_product");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_products_to_categories_delete_trigger_category");
    }
}
