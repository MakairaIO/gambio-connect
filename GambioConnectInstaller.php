<?php

namespace GXModules\Makaira\GambioConnect;

use Doctrine\DBAL\Connection;
use GXModules\Makaira\GambioConnect\App\ChangesService;

class GambioConnectInstaller
{
    public function __construct(
        protected Connection $connection
    ) {}
    
    
    public function onInstallation() {
        $this->connection->executeStatement(
            "CREATE TABLE IF NOT EXISTS `" . ChangesService::TABLE_NAME .  "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `gambio_id` varchar(255) NOT NULL,
                `type` varchar(255) NOT NULL,
                `comment` varchar(255) NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `consumed_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX idx_unique_gambio_id_type (gambio_id, type)
              )"
        );
    }
    
    public function onUninstallation() {
        $this->connection->executeStatement(
            "DROP TABLE IF EXISTS " . ChangesService::TABLE_NAME
        );
    }
}