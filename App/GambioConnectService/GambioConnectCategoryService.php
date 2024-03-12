<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use Doctrine\DBAL\FetchMode;
use Exception;
use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaCategory;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
use GXModules\Makaira\GambioConnect\App\Mapper\MakairaDataMapper;
use GXModules\Makaira\GambioConnect\App\Service\GambioConnectEntityInterface;

class GambioConnectCategoryService extends GambioConnectService implements GambioConnectEntityInterface
{
    private Language $currentLanguage;


    public function prepareExport(): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $language) {
            $categories = $this->getQuery($language);

            foreach ($categories as $category) {
                $this->connection->executeQuery('CALL makairaChange(' . $category['categories_id'] . ', "category")');
            }
        }
    }

    public function export(): void
    {
        $makairaExports = $this->getEntitiesForExport('category');

        if (!empty($makairaExports)) {
            $languages = $this->getLanguages();

            foreach ($languages as $language) {
                $this->currentLanguage = $language;
                $categories = [];
                foreach ($makairaExports as $export) {
                    if ($export['delete']) {
                        $categories[] = [
                            'categories_id' => $export['gambio_id'],
                            'delete' => true,
                        ];
                    } else {
                        $exportCategory = $this->getQuery($language, [$export])[0];
                        $categories[] = array_merge(
                            $exportCategory ?? [],
                            [
                                'categories_id' => $export['gambio_id'],
                                'delete' => false
                            ]
                        );
                    }
                }

                $documents = [];

                if(!empty($categories)) {
                    foreach ($categories as $category) {
                        try {
                            $category['subcategories'] = $this->getSubCategories($language, $category['categories_id']);
                            $documents[] = $this->pushRevision($category);
                        } catch (Exception $exception) {
                            $this->logger->error("Category Export to Makaira Failed", [
                                'id' => $category['categories_id'],
                                'message' => $exception->getMessage()
                            ]);
                        }
                    }
                    $data = $this->addMultipleMakairaDocuments($documents, $this->currentLanguage);
                    $response = $this->client->pushRevision($data);
                    $this->logger->info(
                        'Makaira Category Documents: '
                        . count($documents)
                        . ' with Status Code '
                        . $response->getStatusCode()
                    );
                    foreach ($categories as $category) {
                        $this->exportIsDone($category['categories_id'], 'category');
                    }
                } else {
                    $this->logger->debug("No exportable Categories where found", [
                        'export' => $makairaExports
                    ]);
                }
            }
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
            ->setParameter('languageId', $language->id());

        if (!empty($makairaChanges)) {
            $ids = array_map(fn ($change) => $change['gambio_id'], $makairaChanges);
            $query->andWhere('categories.categories_id IN (' . implode(',', array_values($ids)) . ')');
        }

        return array_filter(
            $query->execute()->fetchAll(FetchMode::ASSOCIATIVE),
            fn (array $category) => $category['language_id'] == $language->id()
        );
    }

    public function getSubCategories(Language $language, int $parentCategoryId): array
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
            ->setParameter('languageId', $language->id())
            ->where('categories.parent_id = :parentId')
            ->setParameter('parentId', $parentCategoryId);

        return array_filter(
            $query->execute()->fetchAll(FetchMode::ASSOCIATIVE),
            fn (array $category) => $category['language_id'] == $language->id()
        );
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

        $parentCategory = $this->connection->createQueryBuilder()
            ->select('categories_id, parent_id')
            ->from('categories')
            ->where('categories_id = :parent_id')
            ->setParameter('parent_id', $category['parent_id'])
            ->execute()
            ->fetchAll(FetchMode::ASSOCIATIVE);
        if (empty($hierarchy)) {
            $hierarchy = $parentCategory['categories_id'] . '//' . $category['categories_id'];
        } else {
            $hierarchy = $parentCategory['categories_id'] . '//' . $hierarchy;
        }

        return $this->calculateCategoryDepth($parentCategory, $depth, $hierarchy);
    }
}
