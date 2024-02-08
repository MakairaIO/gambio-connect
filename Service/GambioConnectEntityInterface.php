<?php

namespace GXModules\Makaira\GambioConnect\Service;

use Gambio\Admin\Modules\Language\Model\Language;

interface GambioConnectEntityInterface
{
    public function export(): void;

    public function replace(): void;

    public function switch(): void;

    public function prepareExport(): void;

    public function pushRevision(array $entity): void;

    public function getQuery(Language $language, array $makairaChanges = []): array;
}
