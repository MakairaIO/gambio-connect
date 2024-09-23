<?php

namespace GXModules\MakairaIO\MakairaConnect\App\Installer;

use CI_DB_query_builder;

class GambioConnectProductsContentTableInstaller implements GambioConnectTableInstallerInterface
{
    public static function install(CI_DB_query_builder $db): void
    {
        $db->query(
            "
            CREATE TRIGGER makaira_connect_product_content_create_trigger AFTER INSERT on products_content
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product')
        "
        );

        $db->query(
            "
            CREATE TRIGGER makaira_connect_product_content_update_trigger AFTER UPDATE on products_content
            FOR EACH ROW
            CALL makairaChange(NEW.products_id, 'product')
        "
        );

        $db->query(
            "
            CREATE TRIGGER makaira_connect_product_content_delete_trigger AFTER DELETE on products_content
            FOR EACH ROW
            CALL makairaChange(OLD.products_id, 'product')
        "
        );
    }

    public static function uninstall(CI_DB_query_builder $db): void
    {
        $db->query('DROP TRIGGER IF EXISTS makaira_connect_product_content_create_trigger');

        $db->query('DROP TRIGGER IF EXISTS makaira_connect_product_content_update_trigger');

        $db->query('DROP TRIGGER IF EXISTS makaira_connect_product_content_delete_trigger');
    }
}
