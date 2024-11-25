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
            $categories = $categoryReadService->getCategoryList(new \LanguageCode(new \StringType($language->code())));
            /** @var CategoryListItem $category */
            foreach($categories as $category) {
                $this->callStoredProcedure($category->getCategoryId(), 'category');
            }
        }
    }

    public function export(array $changes = []): void
    {
        $this->currentLanguage = $_SESSION['languages_id'];

        $this->currentLanguageCode = $_SESSION['language_code'];

        /** @var \CategoryReadService $categoryReadService */
        $categoryReadService = \StaticGXCoreLoader::getService('CategoryRead');

        if (! empty($changes)) {
            $categories = [];
            foreach ($changes as $export) {
                try {
                    $categoryId = new \IdType($export['gambio_id']);
                    $category = MakairaDataMapper::mapCategory(
                        $export['gambio_id'],
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
            $data = $this->addMultipleMakairaDocuments($categories, $this->currentLanguageCode);
            $response = $this->client->pushRevision($data);

            $this->logger->info(
                'Makaira Category Documents: '
                .count($categories)
                .' with Status Code '
                .$response->getStatusCode()
            );
        }
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
