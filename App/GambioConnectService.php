<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Gambio\Admin\Modules\Language\App\LanguageReadService;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaEntity;
use GXModules\Makaira\GambioConnect\App\Service\GambioConnectService as GambioConnectServiceInterface;

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
        protected ProductVariantsRepository $productVariantsRepository,
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

    private function getMakairaChangesForType(string $type): array
    {
        return $this->connection->createQueryBuilder()
            ->select('gambio_id')
            ->from(ChangesService::TABLE_NAME)
            ->where('type = :type')
            ->setParameter('type', $type)
            ->execute()
            ->fetchAll(FetchMode::ASSOCIATIVE);
    }

    public function addMakairaDocumentWrapper(MakairaEntity $document, ?Language $language = null): array
    {
        return [
            'data' => $document->toArray(),
            'language_id' => $language->code()
        ];
    }

    public function addMultipleMakairaDocuments(array $documents, ?Language $language = null): array
    {
        $data = [
            'items' => [],
            'import_timestamp'  => (new \DateTime())->format('Y-m-d H:i:s'),
            'source_identifier' => 'gambio',
        ];

        foreach($documents as $document) {
            $data['items'][] = $this->addMakairaDocumentWrapper($document, $language);
        }

        return $data;
    }

    public function replace(): void
    {
        $this->client->rebuild(['products']);
    }

    public function switch(): void
    {
        $this->client->switch(['products']);
    }
}
