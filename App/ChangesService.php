<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App;

use Doctrine\DBAL\Connection;
use GXModules\Makaira\GambioConnect\App\Models\Change;

class ChangesService
{
    public const TABLE_NAME = 'makaira_connect_changes';


    function __construct(
        private Connection $connection,
    ) {
    }

    public function createTable(): void
    {
        $this->connection->executeStatement(
            "CREATE TABLE IF NOT EXISTS `" . self::TABLE_NAME .  "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `gambioid` varchar(255) NOT NULL,
                `type` varchar(255) NOT NULL,
                `comment` varchar(255) NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `consumed_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX idx_unique_gambioid_type (gambioid, type)
              )"
        );
    }

    public function dropTable(): void
    {
        $this->connection->executeStatement(
            "DROP TABLE IF EXISTS " . self::TABLE_NAME
        );
    }


    public function dispatch(string $gambioId, string $type, string $comment = ""): bool
    {
        $sql = '
                INSERT INTO ' . self::TABLE_NAME . ' (gambioid, type, comment)
                VALUES (:gambioid, :type, :comment)
                ON DUPLICATE KEY UPDATE
                    gambioid = VALUES(gambioid),
                    type = VALUES(type)
            ';

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue('gambioid', $gambioId, \PDO::PARAM_STR);
        $stmt->bindValue('type', $type, \PDO::PARAM_STR);
        $stmt->bindValue('comment', $comment, \PDO::PARAM_STR);

        $stmt->executeStatement();

        return true;
    }


    public function consume()
    {


        $this->connection->beginTransaction();

        try {
            $sqlSelect = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE consumed_at IS NULL ORDER BY id ASC LIMIT 1';
            $record = $this->connection->fetchAssociative($sqlSelect);

            if ($record !== false) {

                $change = new Change(
                    (int) $record['id'],
                    $record['gambioid'],
                    $record['type'],
                    $record['comment'],
                    $record['created_at'],
                    $record['consumed_at']
                );

                $sqlUpdate = 'UPDATE ' . self::TABLE_NAME .  ' SET consumed_at = now() WHERE id = :id';
                $stmt = $this->connection->prepare($sqlUpdate);
                $stmt->bindValue('id', $change->getId(), \PDO::PARAM_INT);
                $stmt->executeStatement();

                $this->connection->commit();
                return $change;
            }
        } catch (\Exception $e) {
            $this->connection->rollBack();

            echo 'Error: ' . $e->getMessage();
        }

        return null;
    }

    public function delete(int $id): void
    {
        $this->connection->delete(self::TABLE_NAME, ['id' => $id]);
    }
}
