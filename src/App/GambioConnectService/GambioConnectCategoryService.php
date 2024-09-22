<?php

namespace GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;

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
            $categories = $this->getQuery($language->id());

            foreach ($categories as $category) {
                $this->connection->executeQuery('CALL makairaChange(' . $category['categories_id'] . ', "category")');
            }
        }
    }

    public function export(): void
    {
        $makairaExports = $this->getEntitiesForExport('category');

        $this->currentLanguage = $_SESSION['languages_id'];

        $this->currentLanguageCode = $_SESSION['language_code'];

        if (!empty($makairaExports)) {
            $categories = [];
            foreach ($makairaExports as $export) {
                if ($export['delete']) {
                    $categories[] = [
                        'categories_id' => $export['gambio_id'],
                        'delete' => true,
                    ];
                } else {
                    $exportCategory = $this->getQuery($this->currentLanguage, [$export])[0];
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

            foreach ($categories as $category) {
                try {
                    $category['subcategories'] = $this->getSubCategories(
                        $this->currentLanguage,
                        $category['categories_id']
                    );
                    $document = $this->pushRevision($category);
                    if ($document->getId()) {
                        $documents[] = $document;
                    }
                } catch (Exception $exception) {
                    $this->logger->error("Category Export to Makaira Failed", [
                        'id' => $category['categories_id'],
                        'message' => $exception->getMessage()
                    ]);
                }
            }
            $data = $this->addMultipleMakairaDocuments($documents, $this->currentLanguageCode);
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

        if (!empty($makairaChanges)) {
            $ids = array_map(fn ($change) => $change['gambio_id'], $makairaChanges);
            $query->andWhere('categories.categories_id IN (' . implode(',', array_values($ids)) . ')');
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
            ->fetchAssociative();
        if (empty($hierarchy)) {
            if(empty($parentCategory)) {
                $hierarchy = $category['categories_id'];
            } else {
                $hierarchy = $parentCategory['categories_id'] . '//' . $category['categories_id'];
            }
        } else {
            $hierarchy = $parentCategory['categories_id'] . '//' . $hierarchy;
        }

        return $this->calculateCategoryDepth($parentCategory, $depth, $hierarchy);
    }
}
