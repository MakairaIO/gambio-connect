<?php

namespace GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;

use Doctrine\DBAL\FetchMode;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaEntity;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;
use GXModules\MakairaIO\MakairaConnect\App\Mapper\MakairaDataMapper;
use GXModules\MakairaIO\MakairaConnect\App\Service\GambioConnectEntityInterface;

class GambioConnectProductService extends GambioConnectService implements GambioConnectEntityInterface
{
    public string $currentLanguage;

    public string $currentLanguageCode;

    protected array $currencyCodes;

    protected array $customerStatusIds;

    public static array $productRelationTables = [
        'products_attributes',
        'products_content',
        'products_description',
        'products_google_categories',
        'products_graduated_prices',
        'products_hermesoptions',
        'products_images',
        'products_item_codes',
        'products_properties_admin_select',
        'products_properties_combis',
        'products_properties_combis_defaults',
        'products_properties_index',
        'products_quantity_unit',
        'products_to_categories',
        'products_xsell',
    ];

    public function prepareExport(): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $language) {
            $products = $this->getQuery($language->id());

            foreach ($products as $product) {
                $this->connection->executeQuery('CALL makairaChange('.$product['products_id'].', "product")');
            }
        }
    }

    public function export(int $start = 0, int $limit = 1000, bool $lastLanguage = false): void
    {
        $this->currencyCodes = $this->getCurrencyCodes();

        $this->customerStatusIds = $this->getCustomerStatusIds();

        $this->currentLanguage = $_SESSION['languages_id'];

        $this->currentLanguageCode = $_SESSION['language_code'];

        $makairaChanges = $this->getEntitiesForExport('product', $start, $limit);

        if (! empty($makairaChanges)) {
            $products = [];
            foreach ($makairaChanges as $change) {
                if ($change['delete']) {
                    if ($change['gambio_id'] !== 0) {
                        $products[] = [
                            'products_id' => $change['gambio_id'],
                            'delete' => true,
                        ];
                    }
                } else {
                    $products[] = [
                        'products_id' => $change['gambio_id'],
                        'delete' => false,
                    ];
                }
            }

            $documents = [];

            foreach ($products as $product) {
                try {
                    $document = MakairaDataMapper::mapProduct($product);
                    if ($document->getId()) {
                        $documents[] = $document;

                        $variants =
                            $this
                                ->productVariantsRepository
                                ->getProductVariantsByProductId(ProductId::create($product['products_id']));

                        $this->logger->info(
                            'Processing '
                            .count($variants->toArray())
                            .' Variants for '
                            .$product['products_id']
                        );

                        foreach ($variants as $variant) {
                            $documents[] = MakairaDataMapper::mapVariant(
                                $product,
                                $this->currentLanguage,
                                $this->currentLanguageCode,
                                $this->currencyCodes,
                                $this->customerStatusIds,
                                $variant
                            );
                        }

                        foreach ($documents as $document) {
                            $this->logger->info('Prepared Document for Makaira '.get_class($document), [
                                'data' => $document->getId(),
                            ]);
                        }
                    }
                } catch (\Exception $exception) {
                    $this->logger->error('Product Export to Makaira Failed', [
                        'id' => $product['products_id'],
                        'message' => $exception->getMessage(),
                    ]);
                }
            }

            $data = $this->addMultipleMakairaDocuments($documents, $this->currentLanguageCode);

            $this->client->pushRevision($data);

            if($lastLanguage) {
                foreach($makairaChanges as $change) {
                    $this->exportIsDone($change['gambio_id'], 'product');
                }
            }
        }
    }

    public function pushRevision(array $product): MakairaEntity
    {
        return MakairaDataMapper::mapProduct(
            $product,
            $_SESSION['languages_id'],
            $this->currentLanguageCode,
            $this->currencyCodes,
            $this->customerStatusIds
        );
    }

    public function getQuery(string $language, array $makairaChanges = []): array
    {
        $shippingStatusQuery = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('shipping_status');

        $shippingStatusArray = $shippingStatusQuery->execute()->fetchAll(FetchMode::ASSOCIATIVE);

        $productsQuery = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('products');

        if (! empty($makairaChanges)) {
            $ids = array_map(fn ($change) => $change['gambio_id'], $makairaChanges);
            $productsQuery
                ->add('where', $productsQuery->expr()->in('products.products_id', $ids), true);
        }

        $results = $productsQuery->execute()->fetchAll(FetchMode::ASSOCIATIVE);

        if (empty($makairaChanges)) {
            return $results;
        }

        foreach ($results as $index => $result) {
            foreach ($shippingStatusArray as $shippingStatus) {
                if ($shippingStatus['shipping_status_id'] === $result['products_shippingtime']) {
                    $results[$index]['shipping_status'] = $shippingStatus;
                }
            }
            foreach (self::$productRelationTables as $relationTable) {
                $query = $this->connection->createQueryBuilder()
                    ->select('*')
                    ->from($relationTable)
                    ->where('products_id = :productsId')
                    ->setParameter('productsId', $result['products_id']);

                if ($relationTable === 'products_description' || $relationTable === 'products_properties_index') {
                    $query
                        ->andWhere($relationTable.'.language_id = :languageId')
                        ->setParameter('languageId', $language);
                }

                if ($relationTable === 'products_to_categories') {
                    $query->join(
                        $relationTable,
                        'categories_description',
                        'categories_description',
                        $relationTable.'.categories_id = categories_description.categories_id'
                    )
                        ->andWhere('categories_description.language_id = :languageId')
                        ->setParameter('languageId', $language->id());
                }

                $relationResult = $query->execute()->fetchAll(FetchMode::ASSOCIATIVE);

                if (count($relationResult) === 1) {
                    $relationResult = $relationResult[0];
                }

                $results[$index][$relationTable] = $relationResult;
            }
        }

        return $results;
    }
}
