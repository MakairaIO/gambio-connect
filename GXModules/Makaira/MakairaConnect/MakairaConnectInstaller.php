<?php

namespace GXModules\Makaira\MakairaConnect;

use CI_DB_query_builder;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\ChangesService;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectCategoriesDescriptionTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectCategoriesFilterTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectCategoriesTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectManufacturersInfoTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsAttributesTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsContentTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsDescriptionTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsGoogleCategoriesTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsGraduatedPricesTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsHermesoptionsTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsImagesTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsPropertiesAdminSelectTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsPropertiesCombisDefaultTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsPropertiesCombisTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsPropertiesIndexTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsQuantityUnitTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Installer\GambioConnectProductsToCategoriesTableInstaller;
use GXModules\Makaira\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\MakairaConnect\App\Installer\GambioConnectManufacturersTableInstaller;
use GXModules\Makaira\MakairaConnect\App\Installer\GambioConnectProductsItemCodesTableInstaller;
use GXModules\Makaira\MakairaConnect\App\Installer\GambioConnectProductsXsellTableInstaller;

class MakairaConnectInstaller
{
    public static function onInstallation(CI_DB_query_builder $db)
    {
        $db->query(
            "CREATE TABLE IF NOT EXISTS `" . ChangesService::TABLE_NAME .  "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `gambio_id` varchar(255) NOT NULL,
                `type` varchar(255) NOT NULL,
                `comment` varchar(255) NOT NULL DEFAULT '',
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `consumed_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
              )"
        );

        $db->query("
        CREATE PROCEDURE makairaChange (IN id INT, IN entity_type VARCHAR(255))
	BEGIN
		DECLARE entries INTEGER DEFAULT 0;
        
        SELECT count(*) INTO entries from `makaira_connect_changes` where gambio_id = id and `type` = entity_type;
        
        IF entries < 1 THEN
			INSERT INTO `makaira_connect_changes` (gambio_id, `type`) VALUES (id, entity_type);
		END IF;
    END;
            ");

        GambioConnectProductsTableInstaller::install($db);

        GambioConnectProductsAttributesTableInstaller::install($db);

        GambioConnectProductsContentTableInstaller::install($db);

        GambioConnectProductsDescriptionTableInstaller::install($db);

        GambioConnectProductsGoogleCategoriesTableInstaller::install($db);

        GambioConnectProductsGraduatedPricesTableInstaller::install($db);

        GambioConnectProductsHermesoptionsTableInstaller::install($db);

        GambioConnectProductsImagesTableInstaller::install($db);

        GambioConnectProductsItemCodesTableInstaller::install($db);

        GambioConnectProductsPropertiesAdminSelectTableInstaller::install($db);

        GambioConnectProductsPropertiesCombisTableInstaller::install($db);

        GambioConnectProductsPropertiesCombisDefaultTableInstaller::install($db);

        GambioConnectProductsPropertiesIndexTableInstaller::install($db);

        GambioConnectProductsQuantityUnitTableInstaller::install($db);

        GambioConnectProductsToCategoriesTableInstaller::install($db);

        GambioConnectProductsXsellTableInstaller::install($db);

        GambioConnectManufacturersTableInstaller::install($db);

        GambioConnectManufacturersInfoTableInstaller::install($db);

        GambioConnectCategoriesTableInstaller::install($db);

        GambioConnectCategoriesDescriptionTableInstaller::install($db);

        GambioConnectCategoriesFilterTableInstaller::install($db);
    }


    public static function onUninstallation(CI_DB_query_builder $db)
    {
        $db->query("DROP TABLE IF EXISTS " . ChangesService::TABLE_NAME);

        $db->query("DROP PROCEDURE IF EXISTS makairaChange;");

        GambioConnectProductsTableInstaller::uninstall($db);

        GambioConnectProductsAttributesTableInstaller::uninstall($db);

        GambioConnectProductsContentTableInstaller::uninstall($db);

        GambioConnectProductsDescriptionTableInstaller::uninstall($db);

        GambioConnectProductsGoogleCategoriesTableInstaller::uninstall($db);

        GambioConnectProductsGraduatedPricesTableInstaller::uninstall($db);

        GambioConnectProductsHermesoptionsTableInstaller::uninstall($db);

        GambioConnectProductsImagesTableInstaller::uninstall($db);

        GambioConnectProductsItemCodesTableInstaller::uninstall($db);

        GambioConnectProductsPropertiesAdminSelectTableInstaller::uninstall($db);

        GambioConnectProductsPropertiesCombisTableInstaller::uninstall($db);

        GambioConnectProductsPropertiesCombisDefaultTableInstaller::uninstall($db);

        GambioConnectProductsPropertiesIndexTableInstaller::uninstall($db);

        GambioConnectProductsQuantityUnitTableInstaller::uninstall($db);

        GambioConnectProductsToCategoriesTableInstaller::uninstall($db);

        GambioConnectProductsXsellTableInstaller::uninstall($db);

        GambioConnectManufacturersTableInstaller::uninstall($db);

        GambioConnectManufacturersInfoTableInstaller::uninstall($db);

        GambioConnectCategoriesTableInstaller::uninstall($db);

        GambioConnectCategoriesDescriptionTableInstaller::uninstall($db);

        GambioConnectCategoriesFilterTableInstaller::uninstall($db);

        foreach (ModuleConfigService::getModuleConfigKeys() as $key) {
            $db->query("DELETE FROM gx_configurations where `key` = '$key'");
        }
    }
}
