<?php

namespace GXModules\Makaira\MakairaConnect\App\Service;

use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\MakairaConnect\App\Documents\MakairaEntity;

interface GambioConnectEntityInterface
{
    public function export(): void;

    public function prepareExport(): void;

    public function pushRevision(array $entity): MakairaEntity;

    public function getQuery(Language $language, array $makairaChanges = []): array;
}
