<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaCategory;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
use GXModules\Makaira\GambioConnect\Service\GambioConnectEntityInterface;

class GambioConnectCategoryService extends GambioConnectService implements GambioConnectEntityInterface
{
    
    public function export(int $categoryId = null): void
    {
        if (!$categoryId) {
            $this->exportAll();
        } else {
            $this->exportCategory($categoryId);
        }
    }
    
    
    public function exportAll(): void
    {
        $languages = $this->languageReadService->getLanguages();
        
        foreach ($languages as $language) {
            $categories = $this->getQuery($language);
            
            foreach ($categories as $category) {
                $this->pushRevision($category);
            }
        }
    }
    
    public function exportCategory(int $categoryId): void
    {
        $languages = $this->languageReadService->getLanguages();
        
        foreach($languages as $language) {
            $this->pushRevision($this->getQuery($language, $categoryId));
            
        }
    }
    
    private function pushRevision(array $category): void
    {
        $makairaCategory = MakairaCategory::mapFromCategory($category);
        
        $data = $this->addMakairaDocumentWrapper($makairaCategory);
        
        $this->client->push_revision($data);
        
        $this->logger->info(\GuzzleHttp\json_encode($data));
    }
    
    
    public function replace(): void
    {
        $this->client->rebuild(['category']);
    }
    
    
    public function switch(): void
    {
        $this->client->rebuild(['category']);
    }
    
    
    /**
     * @param Language $language
     *
     * @return \mixed[][]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getQuery(Language $language, int|null $category_id = null): array
    {
        $query = $this->connection->createQueryBuilder()
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
            ->setParameter('languageId', $language->id());
        
        if($category_id) {
            $query->where('categories.categories_id = :categoriesId')
                ->setParameter('categoriesId', $category_id);
        }
        
        return $query->fetchAllAssociative();
    }
}