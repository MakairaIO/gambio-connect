<?php

declare(strict_types=1);

namespace GXModules\MakairaIO\MakairaConnect\App;

use Doctrine\DBAL\Connection;
use GXModules\MakairaIO\MakairaConnect\App\Models\Change;

class ChangesService
{
    public const TABLE_NAME = 'makaira_connect_changes';

    public function __construct(
        private Connection $connection,
    ) {}

    public function dispatch(string $gambioId, string $type, string $comment = ''): bool
    {
        $sql = '
                INSERT INTO '.self::TABLE_NAME.' (gambio_id, type, comment)
                VALUES (:gambioId, :type, :comment)
                ON DUPLICATE KEY UPDATE
                    gambio_id = VALUES(gambioId),
                    type = VALUES(type)
            ';

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue('gambioId', $gambioId, \PDO::PARAM_STR);
        $stmt->bindValue('type', $type, \PDO::PARAM_STR);
        $stmt->bindValue('comment', $comment, \PDO::PARAM_STR);

        $stmt->executeStatement();

        return true;
    }

    public function consume()
    {

        $this->connection->beginTransaction();

        try {
            $sqlSelect = 'SELECT * FROM '.self::TABLE_NAME.' WHERE consumed_at IS NULL ORDER BY id ASC LIMIT 1';
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

                $sqlUpdate = 'UPDATE '.self::TABLE_NAME.' SET consumed_at = now() WHERE id = :id';
                $stmt = $this->connection->prepare($sqlUpdate);
                $stmt->bindValue('id', $change->getId(), \PDO::PARAM_INT);
                $stmt->executeStatement();

                $this->connection->commit();

                return $change;
            }
        } catch (\Exception $e) {
            $this->connection->rollBack();

            echo 'Error: '.$e->getMessage();
        }

        return null;
    }

    public function delete(int $id): void
    {
        $this->connection->delete(self::TABLE_NAME, ['id' => $id]);
    }

    public function getQueueLength(): int
    {
        $sqlSelect = 'SELECT count(id) FROM '.self::TABLE_NAME;
        $queueLength = $this->connection->fetchOne($sqlSelect);

        return (int) $queueLength;
    }
}
