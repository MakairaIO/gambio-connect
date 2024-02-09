<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaEntity;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaProduct;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
use GXModules\Makaira\GambioConnect\App\Mapper\MakairaDataMapper;
use GXModules\Makaira\GambioConnect\Service\GambioConnectEntityInterface;

class GambioConnectProductService extends GambioConnectService implements GambioConnectEntityInterface
{
    
    public Language $currentLanguage;
    
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
        'products_xsell'
    ];
    
    public function prepareExport(): void
    {
        $languages = $this->languageReadService->getLanguages();

        foreach ($languages as $language) {
            $products = $this->getQuery($language);

            foreach ($products as $product) {
                $this->connection->executeQuery('CALL makairaChange(' . $product['products_id'] . ', "product")');
            }
        }
    }
    
    public function export(): void
    {
        $languages = $this->languageReadService->getLanguages();

        $makairaChanges = $this->getEntitiesForExport('product');

        if (!empty($makairaChanges)) {
            foreach ($languages as $language) {
                $this->currentLanguage = $language;
                $products = $this->getQuery($language, $makairaChanges);

                $documents = [];

                foreach ($products as $product) {
                    try {
                    $documents[] = $this->pushRevision($product);

                    $variants = $this->productVariantsRepository->getProductVariantsByProductId(ProductId::create($product['products_id']));

                    $this->logger->info('Processing ' . count($variants->toArray()). ' Variants for ' . $product['products_id']);

                    foreach($variants as $variant) {
                        $documents[] = MakairaDataMapper::mapVariant($product,$variant);
                    }

                    foreach($documents as $document) {
                        $this->logger->info('Prepared Document for Makaira ' . get_class($document), [
                            'data' => $document->getId()
                        ]);
                    }

                    }catch(\Exception $exception) {
                        $this->logger->error("Product Export to Makaira Failed", [
                            'id' => $product['products_id'],
                            'message' => $exception->getMessage()
                        ]);
                    }

                    $data = $this->addMultipleMakairaDocuments($documents, $this->currentLanguage);

                    $this->client->push_revision($data);

                    $this->exportIsDone($product['products_id'], 'product');
                }
            }
        }
    }
    
    public function pushRevision(array $product): MakairaEntity
    {
        return MakairaDataMapper::mapProduct($product);
    }
    
    public function getQuery(Language $language, array $makairaChanges = []): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('products');

        if (!empty($makairaChanges)) {
            $ids = array_map(fn ($change) => $change['gambio_id'], $makairaChanges);
            $query
                ->add('where', $query->expr()->in('products.products_id', $ids), true);
        }

        $results = $query->execute()->fetchAll(FetchMode::ASSOCIATIVE);

        if (empty($makairaChanges)) {
            return $results;
        }

        foreach ($results as $index => $result) {
            foreach (self::$productRelationTables as $relationTable) {
                $query = $this->connection->createQueryBuilder()
                    ->select('*')
                    ->from($relationTable)
                    ->where('products_id = :productsId')
                    ->setParameter('productsId', $result['products_id']);

                if ($relationTable === 'products_description' || $relationTable === 'products_properties_index') {
                    $query
                        ->andWhere($relationTable . '.language_id = :languageId')
                        ->setParameter('languageId', $language->id());
                }

                $relationResult = $query->execute()->fetchAll(FetchMode::ASSOCIATIVE);

                if (count($relationResult) === 1) {
                    $results[$index][$relationTable] = $relationResult[0];
                } else {
                    $results[$index][$relationTable] = $relationResult;
                }
            }
        }

        return $results;
    }
}