<?php

namespace GXModules\Makaira\GambioConnect;

use Doctrine\DBAL\Connection;
use GXModules\Makaira\GambioConnect\App\ChangesService;

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
            INSERT INTO `makaira_connect_changes` (gambio_id, `type`) VALUES (products_id, entity_type);
            ");
        
        $this->connection->executeStatement("CREATE TRIGGER product_create_trigger AFTER INSERT ON products
                FOR EACH ROW
                CALL makairaChange(NEW.products_id, 'product');");
    }
    
    
    public function onUninstallation()
    {
        $this->connection->executeStatement("DROP TABLE IF EXISTS " . ChangesService::TABLE_NAME);
        
        $this->connection->executeStatement("DROP PROCEDURE IF EXISTS makairaProduct;");
        
        $this->connection->executeStatement("DROP TRIGGER IF EXISTS product_create_trigger;");
    }
}