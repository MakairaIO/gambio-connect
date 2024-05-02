<?php

namespace GXModules\Makaira\MakairaConnect\App\Installer;

use CI_DB_query_builder;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectTableInstallerInterface;

class GambioConnectManufacturersTableInstaller implements GambioConnectTableInstallerInterface
{
    public static function install(CI_DB_query_builder $db): void
    {
        $db->query("
        CREATE TRIGGER makaira_connect_manufacturers_create_trigger AFTER INSERT on manufacturers
        FOR EACH ROW
        CALL makairaChange(NEW.manufacturers_id, 'manufacturer')
        ");

        $db->query("
        CREATE TRIGGER makaira_connect_manufacturers_update_trigger AFTER UPDATE on manufacturers
        FOR EACH ROW
        CALL makairaChange(NEW.manufacturers_id, 'manufacturer')
        ");

        $db->query("
        CREATE TRIGGER makaira_connect_manufacturers_delete_trigger AFTER DELETE on manufacturers
        FOR EACH ROW
        CALL makairaChange(OLD.manufacturers_id, 'manufacturer')
        ");
    }

    public static function uninstall(CI_DB_query_builder $db): void
    {
        $db->query("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_create_trigger");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_update_trigger");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_delete_trigger");
    }
}
