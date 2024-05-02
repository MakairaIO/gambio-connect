<?php

namespace GXModules\Makaira\MakairaConnect\App\Installer;

use CI_DB_query_builder;use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectTableInstallerInterface;

class GambioConnectCategoriesDescriptionTableInstaller implements GambioConnectTableInstallerInterface
{
    public static function install(CI_DB_query_builder $db): void
    {
        $db->query("
        CREATE TRIGGER makaira_connect_categories_description_create_trigger AFTER INSERT on categories_description
        FOR EACH ROW
        CALL makairaChange(NEW.categories_id, 'category')
        ");

        $db->query("
        CREATE TRIGGER makaira_connect_categories_description_update_trigger AFTER UPDATE on categories_description
        FOR EACH ROW
        CALL makairaChange(NEW.categories_id, 'category')
        ");

        $db->query("
        CREATE TRIGGER makaira_connect_categories_description_delete_trigger AFTER DELETE on categories_description
        FOR EACH ROW
        CALL makairaChange(OLD.categories_id, 'category')
        ");
    }

    public static function uninstall(CI_DB_query_builder $db): void
    {
        $db->query("DROP TRIGGER IF EXISTS makaira_connect_categories_description_create_trigger");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_categories_description_update_trigger");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_categories_description_delete_trigger");
    }
}
