<?php

declare(strict_types=1);

namespace GXModules\MakairaIO\MakairaConnect\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Gambio\Admin\Modules\Language\Model\Collections\Languages;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\ProductVariantsRepository;
use Gambio\Core\Language\Services\LanguageService;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaEntity;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectImporterConfigService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectProductService;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService\GambioConnectPublicFieldsService;
use GXModules\MakairaIO\MakairaConnect\App\Service\GambioConnectService as GambioConnectServiceInterface;

/**
 * Class GambioConnectService
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



    public function exportToMakaira(array $changes): void
    {
        $documents = [];

        $productService = $this->getProductService();

        $categoryService = $this->getCategoryService();

        $manufacturerService = $this->getManufacturerService();

        $this->logger->debug("Received Changes", [$changes]);

        foreach ($changes as $change) {
            $this->logger->debug("Processing Change", [
                'change' => $change,
                'type' => $change['type'],
                'id' => $change['gambio_id'],
            ]);
            switch ($change['type']) {
                case 'product':
                    $documents[] = array_merge($documents, $productService->exportDocument($change));
                    break;
                case 'category':
                    $documents[] = $categoryService->exportDocument($change);
                    break;
                case 'manufacturer':
                    $documents[] = $manufacturerService->exportDocument($change);
                    break;
            }
        }

        $this->executeDocumentsInChunks($documents);
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

    public function callStoredProcedure(int $id, string $type): void
    {
        $this->connection->executeQuery('CALL makairaChange(' . $id . ',"' . $type . '")');
    }

    public function exportIsDoneForType(string $type)
    {
        $this->connection->delete(ChangesService::TABLE_NAME, [
            'type' => $type,
        ]);
    }

    protected function exportIsDone(int $gambio_id, string $type): void
    {
        $this->connection->delete(ChangesService::TABLE_NAME, [
            'gambio_id' => $gambio_id,
            'type' => $type,
        ]);
    }

    protected function getEntitiesForExport(string $type, int $start = 0, int $limit = 1000): array
    {
        $changes = $this->getMakairaChangesForType($type, $start, $limit);
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

    protected function getCurrencyCodes(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('code')
            ->from('currencies')
            ->execute()
            ->fetchAll(FetchMode::ASSOCIATIVE);
    }

    protected function getCustomerStatusIds(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('customers_status_id')
            ->from('customers_status')
            ->groupBy('customers_status_id')
            ->execute()
            ->fetchAll(FetchMode::ASSOCIATIVE);
    }

    private function getMakairaChangesForType(string $type, int $start = 0, int $limit = 100): array
    {
        return $this->connection->createQueryBuilder()
            ->select('gambio_id')
            ->from(ChangesService::TABLE_NAME)
            ->where('type = :type')
            ->setParameter('type', $type)
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->execute()
            ->fetchAll(FetchMode::ASSOCIATIVE);
    }

    protected function getShippingInformation($languageId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('shipping_status_name, shipping_status_image, shipping_status_id, info_link_active')
            ->groupBy('language_id')
            ->from(TABLE_SHIPPING_STATUS)
            ->where('language_id = :language_id')
            ->setParameter('language_id', $languageId)
            ->execute()
            ->fetchAll(FetchMode::ASSOCIATIVE);
    }

    public function addMakairaDocumentWrapper(MakairaEntity|array $document, ?string $language = null): array
    {
        $data = [];

        if(is_array($document)) {
            $data['delete'] = $document['delete'];
            unset($document['delete']);
            $data['data'] = $document;
        } else {
            $data['delete'] = $document->isDelete();
            $data['data'] = $document->toArray();
        }

        if($data['delete'] === null) {
            $data['delete'] = false;
        }

        $data['language_id'] = $language;

        $this->logger->debug("Added Document Wrapper", [
            'data' => $data,
            'original' => is_array($document) ? $document : $document->toArray(),
        ]);

        return $data;
    }

    public function executeDocumentsInChunks(array $documents): void
    {
        $chunks = array_chunk($documents, 2);
        $this->logger->debug('Divided Documents in ' . count($chunks) . ' Chunks');
        foreach($chunks as $index => $chunk) {
            $this->logger->debug('Preparing Chunk ' . $index);
            $payload = $this->addMultipleMakairaDocuments($chunk, $_GET['language']);
            if(!empty($payload)) {
                $this->logger->debug('Processing Chunk ' . $index, [
                    'payload' => $payload,
                ]);
                $this->logger->debug("Payload Size: " . mb_strlen(json_encode($payload)));
                try {
                    $response = $this->client->pushRevision($payload);
                }catch(\Exception $exception) {
                    $this->logger->debug("Failed Chunk Processing: " . $exception->getMessage(), [
                        'payload' => $payload,
                    ]);
                }
                $this->logger->debug($response, [
                    'payload' => $payload,
                ]);
                $this->logger->debug('Chunk ' . $index . ' Processed');
            } else {
                $this->logger->debug('Chunk ' . $index . ' Empty');
            }
        }
    }

    public function addMultipleMakairaDocuments(array $documents, ?string $language = null): array
    {
        $data = [
            'items' => array_map(function ($document) use($language) {
                return $this->addMakairaDocumentWrapper($document, $language);
            }, $documents),
            'import_timestamp' => (new \DateTime)->format('Y-m-d H:i:s'),
            'source_identifier' => 'gambio',
        ];

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
