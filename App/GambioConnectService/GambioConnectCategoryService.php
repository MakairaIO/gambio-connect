<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use Exception;
use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\GambioConnect\App\ChangesService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
use GXModules\Makaira\GambioConnect\App\Mapper\MakairaDataMapper;
use GXModules\Makaira\GambioConnect\Service\GambioConnectEntityInterface;

class GambioConnectCategoryService extends GambioConnectService implements GambioConnectEntityInterface
{
    private Language $currentLanguage;
    
    
    public function prepareExport(): void
    {
        $languages = $this->languageReadService->getLanguages();

        foreach ($languages as $language) {
            $categories = $this->getQuery($language);

            foreach ($categories as $category) {
                $this->connection->executeQuery('CALL makairaChange(' . $category['categories_id'] . ', "category")');
            }
        }
    }
    
    public function export(): void
    {
        $languages = $this->languageReadService->getLanguages();
        
        $makairaExports = $this->getEntitiesForExport('category');
        
        if(!empty($makairaExports)) {
            foreach ($languages as $language) {
                $this->currentLanguage = $language;
                $categories            = $this->getQuery($language, $makairaExports);
                
                foreach ($categories as $category) {
                    $this->pushRevision($category);
                    $this->exportIsDone($category['categories_id'], 'category');
                }
            }
        }
    }
    
    
    /**
     * @throws Exception
     */
    public function pushRevision(array $category): void
    {
        $hierarchy = $this->calculateCategoryDepth($category);

        $makairaCategory = MakairaDataMapper::mapCategory($category, $hierarchy, $this->currentLanguage);
        $data            = $this->addMakairaDocumentWrapper($makairaCategory, $this->currentLanguage);
        
        $response = $this->client->push_revision($data);
        
        $this->logger->info("Makaira Categories Status for " . $category['categories_id'] . ": "
                            . $response->getStatusCode());
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
    public function getQuery(Language $language, array $makairaChanges = []): array
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
        
        if(!empty($makairaChanges)) {
            $ids = array_map(fn($change) => $change['gambio_id'], $makairaChanges);
            $query->where('categories.categories_id IN (:ids)')
                ->setParameter('ids', implode(',', array_values($ids)));
        }
        
        return $this->executeQuery($query);
    }
    
    
    private function getSubcategories(int $category_id): array
    {
        return $this->executeQuery($this->connection->createQueryBuilder()
            ->select('categories_id')
            ->from('categories')
            ->where('parent_id = :categories_id')
            ->setParameter('categories_id', $category_id));
    }
    
    
    private function calculateCategoryDepth(array $category, int $depth = 1, string $hierarchy = ''): array
    {
        if (empty($category['parent_id'])) {
            return [
                'depth'     => $depth,
                'hierarchy' => empty($hierarchy) ? $category['categories_id'] : $hierarchy,
            ];
        }
        
        $depth += 1;
        
        $parentCategory = $this->executeQuery($this->connection->createQueryBuilder()
            ->select('categories_id, parent_id')
            ->from('categories')
            ->where('categories_id = :parent_id')
            ->setParameter('parent_id', $category['parent_id']));
        
        if (empty($hierarchy)) {
            $hierarchy = $parentCategory['categories_id'] . '//' . $category['categories_id'];
        } else {
            $hierarchy = $parentCategory['categories_id'] . '//' . $hierarchy;
        }
        
        return $this->calculateCategoryDepth($parentCategory, $depth, $hierarchy);
    }
}
