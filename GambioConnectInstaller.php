<?php

namespace GXModules\Makaira\GambioConnect;

use Doctrine\DBAL\Connection;
use GXModules\Makaira\GambioConnect\App\ChangesService;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectManufacturersInfoTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectManufacturersTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsAttributesTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsContentTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsDescriptionTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsGoogleCategoriesTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsGraduatedPricesTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsHermesoptionsTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsImagesTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsPropertiesAdminSelectTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsPropertiesCombisDefaultTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsPropertiesCombisTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsPropertiesIndexTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsQuantityUnitTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsItemCodesTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsToCategoriesTableInstaller;
use GXModules\Makaira\GambioConnect\Installer\GambioConnectProductsXsellTableInstaller;

class GambioConnectInstaller
{
    public function __construct(
        protected Connection $connection
    ) {
    }
    
    
    public function onInstallation()
    {
        $this->connection->executeStatement("CREATE TABLE IF NOT EXISTS `" . ChangesService::TABLE_NAME . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `gambio_id` varchar(255) NOT NULL,
                `type` varchar(255) NOT NULL,
                `comment` varchar(255) NOT NULL DEFAULT '',
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `consumed_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
              )");
        
        $this->connection->executeStatement("
        CREATE PROCEDURE makairaChange (IN id INT, IN entity_type VARCHAR(255))
	BEGIN
		DECLARE entries INTEGER DEFAULT 0;
        
        SELECT count(*) INTO entries from `makaira_connect_changes` where gambio_id = id and `type` = entity_type;
        
        IF entries < 1 THEN
			INSERT INTO `makaira_connect_changes` (gambio_id, `type`) VALUES (id, entity_type);
		END IF;
    END;
            ");
        
        GambioConnectProductsTableInstaller::install($this->connection);
        
        GambioConnectProductsAttributesTableInstaller::install($this->connection);
        
        GambioConnectProductsContentTableInstaller::install($this->connection);
        
        GambioConnectProductsDescriptionTableInstaller::install($this->connection);
        
        GambioConnectProductsGoogleCategoriesTableInstaller::install($this->connection);
        
        GambioConnectProductsGraduatedPricesTableInstaller::install($this->connection);
        
        GambioConnectProductsHermesoptionsTableInstaller::install($this->connection);
        
        GambioConnectProductsImagesTableInstaller::install($this->connection);
        
        GambioConnectProductsItemCodesTableInstaller::install($this->connection);
        
        GambioConnectProductsPropertiesAdminSelectTableInstaller::install($this->connection);
        
        GambioConnectProductsPropertiesCombisTableInstaller::install($this->connection);
        
        GambioConnectProductsPropertiesCombisDefaultTableInstaller::install($this->connection);
        
        GambioConnectProductsPropertiesIndexTableInstaller::install($this->connection);
        
        GambioConnectProductsQuantityUnitTableInstaller::install($this->connection);
        
        GambioConnectProductsToCategoriesTableInstaller::install($this->connection);
        
        GambioConnectProductsXsellTableInstaller::install($this->connection);
        
        GambioConnectManufacturersTableInstaller::install($this->connection);
        
        GambioConnectManufacturersInfoTableInstaller::install($this->connection);
    }
    
    
    public function onUninstallation()
    {
        $this->connection->executeStatement("DROP TABLE IF EXISTS " . ChangesService::TABLE_NAME);
        
        $this->connection->executeStatement("DROP PROCEDURE IF EXISTS makairaChange;");
        
        GambioConnectProductsTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsAttributesTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsContentTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsDescriptionTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsGoogleCategoriesTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsGraduatedPricesTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsHermesoptionsTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsImagesTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsItemCodesTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsPropertiesAdminSelectTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsPropertiesCombisTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsPropertiesCombisDefaultTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsPropertiesIndexTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsQuantityUnitTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsToCategoriesTableInstaller::uninstall($this->connection);
        
        GambioConnectProductsXsellTableInstaller::uninstall($this->connection);
        
        GambioConnectManufacturersTableInstaller::uninstall($this->connection);
        
        GambioConnectManufacturersInfoTableInstaller::uninstall($this->connection);
    }
}