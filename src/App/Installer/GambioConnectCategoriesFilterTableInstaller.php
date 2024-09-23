<?php

namespace GXModules\MakairaIO\MakairaConnect\App\Installer;

use CI_DB_query_builder;

class GambioConnectCategoriesFilterTableInstaller implements GambioConnectTableInstallerInterface
{
    public static function install(CI_DB_query_builder $db): void
    {
        $db->query(
            "
        CREATE TRIGGER makaira_connect_categories_filter_create_trigger AFTER INSERT on categories_filter
        FOR EACH ROW
        CALL makairaChange(NEW.categories_id, 'category')
        "
        );

        $db->query(
            "
        CREATE TRIGGER makaira_connect_categories_filter_update_trigger AFTER UPDATE on categories_filter
        FOR EACH ROW
        CALL makairaChange(NEW.categories_id, 'category')
        "
        );

        $db->query(
            "
        CREATE TRIGGER makaira_connect_categories_filter_delete_trigger AFTER DELETE on categories_filter
        FOR EACH ROW
        CALL makairaChange(OLD.categories_id, 'category')
        "
        );
    }

    public static function uninstall(CI_DB_query_builder $db): void
    {
        $db->query('DROP TRIGGER IF EXISTS makaira_connect_categories_filter_create_trigger');

        $db->query('DROP TRIGGER IF EXISTS makaira_connect_categories_filter_update_trigger');

        $db->query('DROP TRIGGER IF EXISTS makaira_connect_categories_filter_delete_trigger');
    }
}
