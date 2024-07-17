<?php

namespace GXModules\MakairaIO\MakairaConnect\App\Installer;

use CI_DB_query_builder;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectTableInstallerInterface;

class GambioConnectProductsGoogleCategoriesTableInstaller implements GambioConnectTableInstallerInterface
{
    public static function install(CI_DB_query_builder $db): void
    {
        $db->query(
            "
            CREATE TRIGGER makaira_connect_products_google_categories_create_trigger AFTER INSERT on products_google_categories
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product')
        "
        );

        $db->query(
            "
            CREATE TRIGGER makaira_connect_products_google_categories_update_trigger AFTER UPDATE on products_google_categories
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product')
        "
        );

        $db->query(
            "
            CREATE TRIGGER makaira_connect_products_google_categories_delete_trigger AFTER DELETE on products_google_categories
            FOR EACH ROW
            CALL makairaChange(OLD.products_id, 'product')
        "
        );
    }

    public static function uninstall(CI_DB_query_builder $db): void
    {
        $db->query("DROP TRIGGER IF EXISTS makaira_connect_products_google_categories_create_trigger");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_products_google_categories_update_trigger");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_products_google_categories_delete_trigger");
    }
}
