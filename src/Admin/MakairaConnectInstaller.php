<?php

namespace GXModules\MakairaIO\MakairaConnect\Admin;

use CI_DB_query_builder;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleConfigService;
use GXModules\MakairaIO\MakairaConnect\App\ChangesService;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectCategoriesDescriptionTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectCategoriesFilterTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectCategoriesTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectManufacturersInfoTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectManufacturersTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsAttributesTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsContentTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsDescriptionTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsGoogleCategoriesTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsGraduatedPricesTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsHermesoptionsTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsImagesTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsItemCodesTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsPropertiesAdminSelectTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsPropertiesCombisDefaultTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsPropertiesCombisTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsPropertiesIndexTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsQuantityUnitTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsToCategoriesTableInstaller;
use GXModules\MakairaIO\MakairaConnect\App\Installer\GambioConnectProductsXsellTableInstaller;

class MakairaConnectInstaller
{
    public static function onInstallation(CI_DB_query_builder $db)
    {
        $db->query(
            'CREATE TABLE IF NOT EXISTS `'.ChangesService::TABLE_NAME."` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `gambio_id` varchar(255) NOT NULL,
                `type` varchar(255) NOT NULL,
                `comment` varchar(255) NOT NULL DEFAULT '',
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `consumed_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
              )"
        );

        $db->query('
        CREATE PROCEDURE IF NOT EXISTS makairaChange (IN id INT, IN entity_type VARCHAR(255))
	BEGIN
		DECLARE entries INTEGER DEFAULT 0;
        
        SELECT count(*) INTO entries from `makaira_connect_changes` where gambio_id = id and `type` = entity_type;
        
        IF entries < 1 THEN
			INSERT INTO `makaira_connect_changes` (gambio_id, `type`) VALUES (id, entity_type);
		END IF;
    END;
            ');

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
        $db->query('DROP TABLE IF EXISTS '.ChangesService::TABLE_NAME);

        $db->query('DROP PROCEDURE IF EXISTS makairaChange;');

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
