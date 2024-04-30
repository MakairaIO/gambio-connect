<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Gambio\Admin\Modules\Language\Model\Collections\Languages;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsRepository;
use Gambio\Core\Language\Services\LanguageService;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaEntity;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectImporterConfigService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectPublicFieldsService;
use GXModules\Makaira\GambioConnect\App\Service\GambioConnectService as GambioConnectServiceInterface;

/**
 * Class GambioConnectService
 *
 * @package GXModules\Makaira\GambioConnect\App
 */
class GambioConnectService implements GambioConnectServiceInterface
{
    public function __construct(
        protected MakairaClient $client,
        protected LanguageService $languageService,
        protected Connection $connection,
        protected MakairaLogger $logger,
        protected ?ProductVariantsRepository $productVariantsRepository = null,
    ) {
    }

    private function getService(string $service): static
    {
        return new $service(
            $this->client,
            $this->languageService,
            $this->connection,
            $this->logger,
            $this->productVariantsRepository
        );
    }

    public function getGambioConnectPublicFieldsService(): GambioConnectPublicFieldsService
    {
        return $this->getService(GambioConnectPublicFieldsService::class);
    }

    public function getImporterConfigService(): GambioConnectImporterConfigService
    {
        return $this->getService(GambioConnectImporterConfigService::class);
    }

    public function getCategoryService(): GambioConnectCategoryService
    {
        return $this->getService(GambioConnectCategoryService::class);
    }

    public function getManufacturerService(): GambioConnectManufacturerService
    {
        return $this->getService(GambioConnectManufacturerService::class);
    }

    public function getProductService(): GambioConnectProductService
    {
        return $this->getService(GambioConnectProductService::class);
    }

    protected function getLanguages(): Languages
    {
        return $this->languageService->getAvailableLanguages();
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
        $changes = $this->getMakairaChangesForType($type);
        foreach ($changes as $index => $change) {
            switch ($type) {
                case 'product':
                    $table = 'products';
                    $key = 'products_id';
                    break;
                case 'category':
                    $table = 'categories';
                    $key = 'categories_id';
                    break;
                case 'manufacturer':
                    $table = 'manufacturers';
                    $key = 'manufacturers_id';
                    break;
            }
            $query = $this->connection->createQueryBuilder()
                ->select('*')
                ->from($table)
                ->where("$table.$key = :gambioId")
                ->setParameter('gambioId', $change['gambio_id'])
                ->execute()
                ->fetchAll(FetchMode::ASSOCIATIVE);

            $changes[$index]['delete'] = empty($query);
        }
        return $changes;
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
            'language_id' => $language->code(),
            'delete' => $document->isDelete()
        ];
    }

    public function addMultipleMakairaDocuments(array $documents, ?Language $language = null): array
    {
        $data = [
            'items' => [],
            'import_timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            'source_identifier' => 'gambio',
        ];

        foreach ($documents as $document) {
            $data['items'][] = $this->addMakairaDocumentWrapper($document, $language);
        }

        $this->logger->debug("Makaira Documents for Debug", $data);

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
