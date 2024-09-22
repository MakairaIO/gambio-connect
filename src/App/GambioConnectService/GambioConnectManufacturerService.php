<?php

namespace GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;

use Doctrine\DBAL\FetchMode;
use Exception;
use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaEntity;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;
use GXModules\MakairaIO\MakairaConnect\App\Mapper\MakairaDataMapper;
use GXModules\MakairaIO\MakairaConnect\App\Service\GambioConnectEntityInterface;

class GambioConnectManufacturerService extends GambioConnectService implements GambioConnectEntityInterface
{
    private string $currentLanguage;

    private string $currentLanguageCode;


    public function prepareExport(): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $language) {
            $manufacturers = $this->getQuery($language->id());

            foreach ($manufacturers as $manufacturer) {
                $this->connection->executeQuery(
                    'CALL makairaChange('
                    . $manufacturer['manufacturers_id']
                    . ', "manufacturer")'
                );
            }
        }
    }


    /**
     * @throws Exception
     */
    public function export(): void
    {
        $this->currentLanguage = $_SESSION['languages_id'];

        $this->currentLanguageCode = $_SESSION['language_code'];

        $makairaExports = $this->getEntitiesForExport('manufacturer');

        if (!empty($makairaExports)) {
            $manufacturers = [];
            foreach ($makairaExports as $export) {
                if ($export['delete']) {
                    $manufacturers[] = [
                        'manufacturers_id' => $export['gambio_id'],
                        'delete' => true
                    ];
                } else {
                    $manufacturers[] = array_merge(
                        $this->getQuery($this->currentLanguage, [$export])[0],
                        [
                            'manufacturers_id' => $export['gambio_id'],
                            'delete' => false
                        ]
                    );
                }
            }

            $documents = [];

            foreach ($manufacturers as $manufacturer) {
                try {
                    $document = $this->pushRevision($manufacturer);
                    if ($document->getId()) {
                        $documents[] = $document;
                    }
                } catch (Exception $exception) {
                    $this->logger->error("Manufacturer Export to Makaira Failed", [
                        'id' => $manufacturer['manufacturers_id'],
                        'message' => $exception->getMessage()
                    ]);
                }
            }
            $data = $this->addMultipleMakairaDocuments($documents, $this->currentLanguage);
            $response = $this->client->pushRevision($data);
            $this->logger->info(
                'Makaira Manufacturer Documents: '
                . count($documents)
                . ' with Status Code '
                . $response->getStatusCode()
            );
            foreach ($manufacturers as $manufacturer) {
                $this->exportIsDone($manufacturer['manufacturers_id'], 'manufacturer');
            }
        }
    }


    /**
     * @throws Exception
     */
    public function pushRevision(array $manufacturer): MakairaEntity
    {
        return MakairaDataMapper::mapManufacturer($manufacturer);
    }


    public function getQuery(string $language, array $makairaChanges = []): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('manufacturers')
            ->leftJoin(
                'manufacturers',
                'manufacturers_info',
                'manufacturers_info',
                'manufacturers.manufacturers_id = manufacturers_info.manufacturers_id'
            )
            ->where('manufacturers_info.languages_id = :languageId')
            ->setParameter('languageId', $language);

        if (!empty($makairaChanges)) {
            $ids = array_map(fn($change) => $change['gambio_id'], $makairaChanges);
            $query->where('manufacturers.manufacturers_id IN (:ids)')
                ->setParameter('ids', implode(',', array_values($ids)));
        }

        return $query->execute()->fetchAll(FetchMode::ASSOCIATIVE);
    }
}
