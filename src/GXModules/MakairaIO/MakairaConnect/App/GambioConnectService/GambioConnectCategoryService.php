<?php

namespace GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;

use CategoryListItem;
use Doctrine\DBAL\FetchMode;
use Exception;
use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaCategory;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;
use GXModules\MakairaIO\MakairaConnect\App\Mapper\MakairaDataMapper;
use GXModules\MakairaIO\MakairaConnect\App\Service\GambioConnectEntityInterface;

class GambioConnectCategoryService extends GambioConnectService implements GambioConnectEntityInterface
{
    private string $currentLanguage;

    private string $currentLanguageCode;

    public function prepareExport(): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $language) {
            /** @var \CategoryReadService $categoryReadService */
            $categoryReadService = \StaticGXCoreLoader::getService('CategoryRead');
            /** @var \CategoryListItemCollection $categories */
            $categories = $categoryReadService->getCategoryList(new \LanguageCode($language->code()));
            /** @var CategoryListItem $category */
            foreach($categories as $category) {
                $this->callStoredProcedure($category->getCategoryId(), 'category');
            }
        }
    }

    public function export(int $start = 0, int $limit = 1000, bool $lastLanguage = false): void
    {
        $makairaExports = $this->getEntitiesForExport('category', $start, $limit);

        $this->currentLanguage = $_SESSION['languages_id'];

        $this->currentLanguageCode = $_SESSION['language_code'];

        /** @var \CategoryReadService $categoryReadService */
        $categoryReadService = \StaticGXCoreLoader::getService('CategoryRead');

        if (! empty($makairaExports)) {
            $categories = [];
            foreach ($makairaExports as $export) {
                if ($export['delete']) {
                    $categories[] = MakairaDataMapper::mapCategory($export['gambio_id'], true, $this->currentLanguageCode);
                } else {
                    try {
                        $categoryId = new \IdType($export['gambio_id']);
                        $category = MakairaDataMapper::mapCategory(
                            $export['gambio_id'],
                            false,
                            $this->currentLanguageCode
                        );

                        /** @var \IdCollection $subCategoryIds */
                        $subCategoryIds = $categoryReadService->getCategoryIdsTree($categoryId);
                        $hierarchy = $category->getCategoriesId() . '//';
                        $depth = 1;
                        foreach ($subCategoryIds as $subCategoryId) {
                            /** @var IdType $subCategoryId */
                            $subCategories = MakairaDataMapper::mapCategory(
                                (int)$subCategoryId,
                                false,
                                $this->currentLanguageCode
                            )->toArray();
                            $hierarchy .= (int)$subCategoryId . '//';
                            $depth++;
                        }
                        $category->setSubCategories($subCategories);
                        $category->setHierarchy($hierarchy);
                        $category->setDepth($depth);

                        $categories[] = $category->toArray();
                    }catch(Exception $e){
                        $this->logger->error('Category Export to Makaira Failed', [
                            'id' => $category->getCategoriesId(),
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            }
            $data = $this->addMultipleMakairaDocuments($categories, $this->currentLanguageCode);
            $response = $this->client->pushRevision($data);

            if($lastLanguage) {
                foreach($makairaExports as $change) {
                    $this->exportIsDone($change['gambio_id'], 'product');
                }
            }

            $this->logger->info(
                'Makaira Category Documents: '
                .count($categories)
                .' with Status Code '
                .$response->getStatusCode()
            );
        }
    }

    /**
     * @throws Exception
     */
    public function pushRevision(array $category): MakairaCategory
    {
        $hierarchy = $this->calculateCategoryDepth($category);

        return MakairaDataMapper::mapCategory($category, $hierarchy, $this->currentLanguage);
    }

    /**
     * @param  Language  $language
     * @return \mixed[][]
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getQuery(string $language, array $makairaChanges = []): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('categories')
            ->leftJoin(
                'categories',
                'categories_description',
                'categories_description',
                'categories.categories_id = categories_description.categories_id'
            )
            ->leftJoin(
                'categories_description',
                'languages',
                'languages',
                'categories_description.language_id = languages.languages_id'
            )
            ->where('categories_description.language_id = :languageId')
            ->setParameter('languageId', $language);

        if (! empty($makairaChanges)) {
            $ids = array_map(fn ($change) => $change['gambio_id'], $makairaChanges);
            $query->andWhere('categories.categories_id IN ('.implode(',', array_values($ids)).')');
        }

        return array_filter(
            $query->execute()->fetchAll(FetchMode::ASSOCIATIVE),
            fn (array $category) => $category['language_id'] == $language
        );
    }

    public function getSubCategories(string $language, int $parentCategoryId): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('categories')
            ->leftJoin(
                'categories',
                'categories_description',
                'categories_description',
                'categories.categories_id = categories_description.categories_id'
            )
            ->leftJoin(
                'categories_description',
                'languages',
                'languages',
                'categories_description.language_id = languages.languages_id'
            )
            ->where('categories_description.language_id = :languageId')
            ->setParameter('languageId', $language)
            ->where('categories.parent_id = :parentId')
            ->setParameter('parentId', $parentCategoryId);

        return array_filter(
            $query->execute()->fetchAll(FetchMode::ASSOCIATIVE),
            fn (array $category) => $category['language_id'] == $language
        );
    }

    private function calculateCategoryDepth(array $category, int $depth = 1, string $hierarchy = ''): array
    {
        if (empty($category['parent_id'])) {
            return [
                'depth' => $depth,
                'hierarchy' => empty($hierarchy) ? $category['categories_id'] : $hierarchy,
            ];
        }

        $depth += 1;

        $parentCategory = $this->connection->createQueryBuilder()
            ->select('categories_id, parent_id')
            ->from('categories')
            ->where('categories_id = :parent_id')
            ->setParameter('parent_id', $category['parent_id'])
            ->execute()
            ->fetchAssociative();
        if (empty($hierarchy)) {
            if (empty($parentCategory)) {
                $hierarchy = $category['categories_id'];
            } else {
                $hierarchy = $parentCategory['categories_id'].'//'.$category['categories_id'];
            }
        } else {
            $hierarchy = $parentCategory['categories_id'].'//'.$hierarchy;
        }

        return $this->calculateCategoryDepth($parentCategory, $depth, $hierarchy);
    }
}
