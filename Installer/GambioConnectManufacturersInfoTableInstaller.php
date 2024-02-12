<?php

namespace GXModules\Makaira\GambioConnect\Installer;

use CI_DB_query_builder;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectTableInstallerInterface;

class GambioConnectManufacturersInfoTableInstaller implements GambioConnectTableInstallerInterface
{

    public static function install(CI_DB_query_builder $db): void
    {
        $db->query("
        CREATE TRIGGER makaira_connect_manufacturers_info_create_trigger AFTER INSERT on manufacturers_info
        FOR EACH ROW
        CALL makairaChange(NEW.manufacturers_id, 'manufacturer')
        ");

        $db->query("
        CREATE TRIGGER makaira_connect_manufacturers_info_update_trigger AFTER INSERT on manufacturers_info
        FOR EACH ROW
        CALL makairaChange(NEW.manufacturers_id, 'manufacturer')
        ");

        $db->query("
        CREATE TRIGGER makaira_connect_manufacturers_info_delete_trigger AFTER DELETE on manufacturers_info
        FOR EACH ROW
        CALL makairaChange(OLD.manufacturers_id, 'manufacturer')
        ");
    }

    public static function uninstall(CI_DB_query_builder $db): void
    {
        $db->query("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_info_create_trigger");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_info_update_trigger");

        $db->query("DROP TRIGGER IF EXISTS makaira_connect_manufacturers_info_delete_trigger");
    }
}
