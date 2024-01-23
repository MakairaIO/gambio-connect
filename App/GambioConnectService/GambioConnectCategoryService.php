<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use Exception;
use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaCategory;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
use GXModules\Makaira\GambioConnect\App\Mapper\MakairaDataMapper;
use GXModules\Makaira\GambioConnect\Service\GambioConnectEntityInterface;

class GambioConnectCategoryService extends GambioConnectService implements GambioConnectEntityInterface
{
    private Language $currentLanguage;
    
    
    /**
     * @throws Exception
     */
    public function export(int $categoryId = null): void
    {
        if (!$categoryId) {
            $this->exportAll();
        } else {
            $this->exportCategory($categoryId);
        }
    }
    
    
    /**
     * @throws Exception
     */
    public function exportAll(): void
    {
        $languages = $this->languageReadService->getLanguages();
        
        foreach ($languages as $language) {
            $this->currentLanguage = $language;
            $categories = $this->getQuery($language);
            
            foreach ($categories as $category) {
                $this->pushRevision($category);
            }
        }
    }
    
    
    /**
     * @throws Exception
     */
    public function exportCategory(int $categoryId): void
    {
        $languages = $this->languageReadService->getLanguages();
        
        foreach($languages as $language) {
            $this->currentLanguage = $language;
            $this->pushRevision($this->getQuery($language, $categoryId));
            
        }
    }
    
    
    /**
     * @throws Exception
     */
    private function pushRevision(array $category): void
    {
        $hierarchy = $this->calculateCategoryDepth($category);
        
        $this->logger->info(json_encode($hierarchy));
        
        $makairaCategory = MakairaDataMapper::mapCategory($category, $hierarchy);
        
        $this->logger->info(json_encode($makairaCategory->toArray()));
        
        exit();
        
        $data = $this->addMakairaDocumentWrapper($makairaCategory);
        
        $this->logger->info(json_encode($data));
        
        $response = $this->client->push_revision($data);
        
        $this->logger->info("Makaira Categories Status for " . $category['categories_id'] . ": " . $response->getStatusCode());
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
    
    private function getSubcategories(int $category_id): array
    {
        return $this->connection->createQueryBuilder()
            ->select('categories_id')
            ->from('categories')
            ->where('parent_id = :categories_id')
            ->setParameter('categories_id', $category_id)
            ->fetchAllAssociative();
    }
    
    private function calculateCategoryDepth(array $category, int $depth = 1, string $hierarchy = ''): array {
        if(empty($category['parent_id'])) {
            return [
                'depth' => $depth,
                'hierarchy' => $category['categories_id']
            ];
        }
        
        $depth += 1;
        
        $parentCategory = $this->connection->createQueryBuilder()
            ->select('categories_id, parent_id')
            ->from('categories')
            ->where('parent_id = :parent_id')
            ->setParameter('parent_id', $category['parent_id'])
            ->fetchOne();
        
        if(empty($hierarchy)) {
            $hierarchy = $parentCategory['categories_id'] . '//' . $category['categories_id'];
        } else {
            $hierarchy = $parentCategory['categories_id'] . '//' . $hierarchy;
        }
        
        return $this->calculateCategoryDepth($parentCategory, $depth, $hierarchy);
    }
}