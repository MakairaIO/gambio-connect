<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App;

use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Admin\Modules\Language\App\LanguageReadService;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionReadService;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaEntity;
use GXModules\Makaira\GambioConnect\Service\GambioConnectService as GambioConnectServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService;
use Doctrine\DBAL\Connection;

/**
 * Class GambioConnectService
 *
 * @package GXModules\Makaira\GambioConnect\App
 */
class GambioConnectService implements GambioConnectServiceInterface
{
    // private ProductRepositoryReader $productReadService;

    public function __construct(
        protected MakairaClient               $client,
        protected LanguageReadService         $languageReadService,
        protected Connection                  $connection,
        protected MakairaLogger               $logger,
        //   ProductRepositoryReader $productReadService,

    ) {
        // $this->productReadService = $productReadService;
    }

    protected function exportIsDone(int $gambio_id, string $type): void
    {
        $this->connection->delete(ChangesService::TABLE_NAME, [
            'gambio_id' => $gambio_id,
            'type' => $type
        ]);
    }

    protected function getEntitiesForExport(string $type): array
    {
        return $this->getMakairaChangesForType($type);
    }

    public function executeQuery(QueryBuilder $queryBuilder): array
    {
        $this->logger->debug("Available Query Builder Methods", [
            get_class_methods($queryBuilder)
        ]);
        return $queryBuilder->fetchAllAssociative();
    }

    private function getMakairaChangesForType(string $type): array
    {
        return $this->executeQuery($this->connection->createQueryBuilder()
            ->select('gambio_id')
            ->from(ChangesService::TABLE_NAME)
            ->where('type = :type')
            ->setParameter('type', $type));
    }

    public function addMakairaDocumentWrapper(MakairaEntity $document, ?Language $language = null): array
    {
        $data = [
            'items' => [
                [
                    'data' => $document->toArray(),
                ],
            ],
            'import_timestamp'  => (new \DateTime())->format('Y-m-d H:i:s'),
            'source_identifier' => 'gambio',
        ];

        if ($language) {
            $data['items'][0]['language_id'] = $language->code();
        }

        return $data;
    }
}
