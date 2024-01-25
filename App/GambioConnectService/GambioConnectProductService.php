<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaProduct;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
use GXModules\Makaira\GambioConnect\App\Mapper\MakairaDataMapper;
use GXModules\Makaira\GambioConnect\Service\GambioConnectEntityInterface;

class GambioConnectProductService extends GambioConnectService implements GambioConnectEntityInterface
{
    
    
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
        $this->exportAll();
    }
    
    
    
    public function exportAll(): void
    {
        $languages = $this->languageReadService->getLanguages();
        
        $makairaChanges = $this->getEntitiesForExport('product');
        
        if(!empty($makairaChanges)) {
            foreach ($languages as $language) {
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
    
    private function pushRevision(array $product): void
    {
        $this->logger->info('Product Data', ['data' => $product]);
        
        $makairaProduct = MakairaDataMapper::mapProduct($product);
        
        $data = $this->addMakairaDocumentWrapper($makairaProduct);
        
        $response = $this->client->push_revision($data);
        
        $this->logger->info('Makaira Product Status for: ' . $product['products_id'] . ': ' . $response->getStatusCode());
    }
    
    private function getQuery(Language $language, array $makairaChanges = []) {
        $query = $this->connection->createQueryBuilder()
            ->select(empty($makairaChanges) ? '*' : 'products.products_id')
            ->from('products')
            ->setParameter('languageId', $language->id())
            ->rightJoin('products', 'products_attributes', 'products_attributes', 'products.products_id = products_attributes.products_id')
            ->rightJoin('products_attributes', 'products_attributes_download', 'products_attributes_download', 'products_attributes.products_attributes_id = products_attributes_download.products_attributes_id')
            ->rightJoin('products', 'products_content', 'products_content', 'products.products_id = products_content.products_id')
            ->rightJoin('products', 'products_description', 'products_description', 'products.products_id = products_description.products_id')
            ->where('products_description.languages_id = :languageId')
            ->rightJoin('products', 'products_google_categories', 'products_google_categories', 'products.products_id = products_google_categories.products_id')
            ->rightJoin('products', 'products_graduated_prices', 'products_graduated_prices', 'products.products_id = products_graduated_prices.products_id')
            ->rightJoin('products', 'products_hermesoptions', 'products_hermesoptions', 'products.products_id = products_hermesoptions.products_id')
            ->rightJoin('products', 'products_images', 'products_images', 'products.products_id = products_images.products_id')
            ->rightJoin('products', 'products_item_codes', 'products_item_codes', 'products.products_id = products_item_codes.products_id')
            ->rightJoin('products', 'products_properties_admin_select', 'products_properties_admin_select', 'products.products_id = products_properties_admin_select.products_id')
            ->rightJoin('products', 'products_properties_combis', 'products_properties_combis', 'products.products_id = products_properties_combis.products_id')
            ->rightJoin('products', 'products_properties_combis_defaults', 'products_properties_combis_defaults', 'products.products_id = products_properties_combis_defaults.products_id')
            ->rightJoin('products', 'products_properties_index', 'products_properties_index',  'products.products_id = products_properties_index.products_id')
            ->where('products_properties_index.language_id = :languageId')
            ->rightJoin('products', 'products_quantity_unit', 'products_quantity_unit', 'products.products_id = products_quantity_unit.products_id')
            ->rightJoin('products', 'products_to_categories', 'products_to_categories', 'products.products_id = products_to_categories.products_id')
            ->rightJoin('products', 'products_xsell', 'products_xsell', 'products.products_id = products_xsell.products_id')
            ;
        
        if(!empty($makairaChanges)) {
            $ids = array_map(fn($change) => $change['gambio_id'], $makairaChanges);
            $query->where('products.products_id IN (:ids)')
                ->setParameter('ids', implode(',', array_values($ids)));
        }
        
        return $query->fetchAllAssociative();
    }
}