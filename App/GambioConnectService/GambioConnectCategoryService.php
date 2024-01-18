<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use GXModules\Makaira\GambioConnect\App\Documents\MakairaCategory;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
use GXModules\Makaira\GambioConnect\Service\GambioConnectEntityInterface;

class GambioConnectCategoryService extends GambioConnectService implements GambioConnectEntityInterface
{
    
    public function export(int $categoryId = null): void
    {
        if (!$categoryId) {
            $this->exportAll();
        }
    }
    
    
    public function exportAll(): void
    {
        $languages = $this->languageReadService->getLanguages();
        
        foreach ($languages as $language) {
            $categories = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('categories')
                ->leftJoin('categories',
                           'categories_description',
                           'categories_description',
                           'categories.categories_id = categories_description.categories_id')
                ->leftJoin('categories_description',
                           'languages',
                           'languages',
                           'categories_description.language_id = languages.languages_id')
                ->where('categories_description.language_id = :languageId')
                ->setParameter('languageId', $language->id())
                ->fetchAllAssociative();
            
            foreach ($categories as $category) {
                $makairaCategory = MakairaCategory::mapFromCategory($category);
                
                $data = $this->addMakairaDocumentWrapper($makairaCategory);
                
                $this->client->push_revision($data);
                
                $this->logger->info(\GuzzleHttp\json_encode($data));
            }
        }
    }
    
    
    public function replace(): void
    {
        // TODO: Implement replace() method.
    }
    
    
    public function switch(): void
    {
        // TODO: Implement switch() method.
    }
}