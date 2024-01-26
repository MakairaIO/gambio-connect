<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Language\Model\Language;
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

        foreach($languages as $language) {
            $products = $this->getQuery($language);

            foreach($products as $product) {
                $this->connection->executeQuery('CALL makairaChange(' . $product['products_id'] . ', "product")');
            }
        }
    }

    public function export(): void
    {
        $languages = $this->languageReadService->getLanguages();

        $makairaChanges = $this->getEntitiesForExport('product');

        if(!empty($makairaChanges)) {
            foreach ($languages as $language) {
                $this->currentLanguage = $language;
                $products = $this->getQuery($language, $makairaChanges);

                foreach ($products as $product) {
                    $this->pushRevision($product);
                    $this->exportIsDone($product['products_id'], 'product');
                }
            }
        }
    }

    public function replace(): void
    {
        $this->client->rebuild(['products']);
    }

    public function switch(): void
    {
        $this->client->switch(['products']);
    }

    public function pushRevision(array $product): void
    {
        $this->logger->info('Product Data', ['data' => $product]);

        $makairaProduct = MakairaDataMapper::mapProduct($product);

        $data = $this->addMakairaDocumentWrapper($makairaProduct, $this->currentLanguage);

        $response = $this->client->push_revision($data);

        $this->logger->info('Makaira Product Status for: ' . $product['products_id'] . ': ' . $response->getStatusCode());
    }

    public function getQuery(Language $language, array $makairaChanges = []): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('products');

        if(!empty($makairaChanges)) {
            $ids = array_map(fn ($change) => $change['gambio_id'], $makairaChanges);
            $query
                ->add('where', $query->expr()->in('products.products_id', $ids), true);
        }

        $results = $query->fetchAllAssociative();

        if(empty($makairaChanges)) {
            return $results;
        }

        foreach($results as $index => $result) {
            foreach(self::$productRelationTables as $relationTable) {
                $query = $this->connection->createQueryBuilder()
                    ->select('*')
                    ->from($relationTable)
                    ->where('products_id = :productsId')
                    ->setParameter('productsId', $result['products_id']);

                if($relationTable === 'products_description' || $relationTable === 'products_properties_index') {
                    $query
                        ->andWhere($relationTable . '.language_id = :languageId')
                        ->setParameter('languageId', $language->id());
                }

                $relationResult = $query->fetchAllAssociative();

                if(count($relationResult) === 1) {
                    $results[$index][$relationTable] = $relationResult[0];
                } else {
                    $results[$index][$relationTable] = $relationResult;
                }
            }
        }

        return $results;
    }
}
