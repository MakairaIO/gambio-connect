<?php

namespace GXModules\MakairaIO\MakairaConnect\App\Installer;

use CI_DB_query_builder;

class GambioConnectProductsTableInstaller
{
    public static function install(CI_DB_query_builder $db): void
    {
        $db->query("
            CREATE TRIGGER makaira_connect_product_create_trigger AFTER INSERT ON products
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product');
        ");

        $db->query("
            CREATE TRIGGER makaira_connect_product_update_trigger AFTER UPDATE ON products
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product');
        ");

        $db->query("
            CREATE TRIGGER makaira_connect_product_delete_trigger AFTER DELETE on products
            FOR EACH ROW
            CALL makairaChange(OLD.products_id, 'product');
        ");
    }

    public static function uninstall(CI_DB_query_builder $db): void
    {
        $db->query("DROP TRIGGER IF EXISTS makaira_connect_product_create_trigger");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_product_update_trigger");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_product_delete_trigger");
    }
}
