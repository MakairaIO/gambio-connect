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
        /** @var \ManufacturerReadService $readService */
        $readService = \StaticGXCoreLoader::getService('ManufacturerRead');

        $manufacturers = $readService->getAll();

        /** @var \ManufacturerInterface $manufacturer */
        foreach($manufacturers as $manufacturer) {
            $this->callStoredProcedure($manufacturer->getId(), 'manufacturer');
        }
    }

    /**
     * @throws Exception
     */
    public function export(array $changes = []): void
    {
        $this->currentLanguage = $_SESSION['languages_id'];

        $this->currentLanguageCode = $_SESSION['language_code'];

        if (! empty($changes)) {
            $manufacturers = [];
            foreach ($changes as $change) {
                try {
                    $manufacturers[] = $this->exportDocument($change);
                }catch (Exception $e){
                    $this->logger->error('Manufacturer Export to Makaira Failed', [
                        'id' => $change['gambio_id'],
                        'message' => $e->getMessage(),
                    ]);
                }
            }

            $data = $this->addMultipleMakairaDocuments($manufacturers, $this->currentLanguageCode);
            $response = $this->client->pushRevision($data);

            $this->logger->info(
                'Makaira Manufacturer Documents: '
                .count($manufacturers)
                .' with Status Code '
                .$response->getStatusCode()
            );
        }
    }

    public function exportDocument(array $change): MakairaEntity
    {
        $this->currentLanguage = $_SESSION['languages_id'];

        $this->currentLanguageCode = $_SESSION['language_code'];

        return MakairaDataMapper::mapManufacturer(
            $change['gambio_id'],
            $this->currentLanguageCode
        );
    }

    /**
     * @throws Exception
     */
    public function pushRevision(array $manufacturer): MakairaEntity
    {
        return MakairaDataMapper::mapManufacturer($manufacturer);
    }
}
