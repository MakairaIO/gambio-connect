<?php

namespace GXModules\MakairaIO\MakairaConnect\App\Service;

use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaEntity;

interface GambioConnectEntityInterface
{
    public function export(): void;

    public function prepareExport(): void;

    public function pushRevision(array $entity): MakairaEntity;

    public function getQuery(string $language, array $makairaChanges = []): array;
}
