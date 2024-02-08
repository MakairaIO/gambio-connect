<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use Doctrine\DBAL\FetchMode;
use Exception;
use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaManufacturer;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
use GXModules\Makaira\GambioConnect\App\Mapper\MakairaDataMapper;
use GXModules\Makaira\GambioConnect\Service\GambioConnectEntityInterface;

class GambioConnectManufacturerService extends GambioConnectService implements GambioConnectEntityInterface
{
    private Language $currentLanguage;



    public function prepareExport(): void
    {
        $languages = $this->languageReadService->getLanguages();

        foreach ($languages as $language) {
            $manufacturers = $this->getQuery($language);

            foreach ($manufacturers as $manufacturer) {
                $this->connection->executeQuery('CALL makairaChange(' . $manufacturer['manufacturers_id'] . ', "manufacturer")');
            }
        }
    }


    /**
     * @throws Exception
     */
    public function export(): void
    {
        $languages = $this->languageReadService->getLanguages();

        $makairaExports = $this->getEntitiesForExport('manufacturer');

        if (!empty($makairaExports)) {
            foreach ($languages as $language) {

                $this->currentLanguage = $language;
                $manufacturers = $this->getQuery($language, $makairaExports);

                foreach ($manufacturers as $manufacturer) {
                    try {
                        $this->pushRevision($manufacturer);
                        $this->exportIsDone($manufacturer['manufacturers_id'], 'manufacturer');
                    } catch (Exception $exception) {
                        $this->logger->error("Manufacturer Export to Makaira Failed", [
                            'id' => $manufacturer['manufacturers_id'],
                            'message' => $exception->getMessage()
                        ]);
                    }
                }
            }
        }
    }


    /**
     * @throws Exception
     */
    public function pushRevision(array $manufacturer): void
    {
        $makairaManufactuer = MakairaDataMapper::mapManufacturer($manufacturer);

        $data = $this->addMakairaDocumentWrapper($makairaManufactuer, $this->currentLanguage);

        $response = $this->client->push_revision($data);

        $this->logger->info('Makaira Manufacturer Status for: ' . $manufacturer['manufacturers_id'] . ': ' . $response->getStatusCode());
    }

    public function replace(): void
    {
        $this->client->rebuild(['manufacturer']);
    }


    public function switch(): void
    {
        $this->client->rebuild(['manufacturer']);
    }


    public function getQuery(Language $language, array $makairaChanges = []): array
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
            ->setParameter('languageId', $language->id());

        if (!empty($makairaChanges)) {
            $ids = array_map(fn ($change) => $change['gambio_id'], $makairaChanges);
            $query->where('manufacturers.manufacturers_id IN (:ids)')
                ->setParameter('ids', implode(',', array_values($ids)));
        }

        return $query->execute()->fetchAll(FetchMode::ASSOCIATIVE);
    }
}
