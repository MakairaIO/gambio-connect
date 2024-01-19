<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaManufacturer;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;

class GambioConnectManufacturerService extends GambioConnectService
{
    private Language $currentLanguage;
    
    
    public function export(int $manufacturerId = null): void
    {
        if (!$manufacturerId) {
            $this->exportAll();
        } else {
            $this->exportManufacturer($manufacturerId);
        }
    }
    
    
    public function exportAll(): void
    {
        $languages = $this->languageReadService->getLanguages();
        
        foreach($languages as $language) {
            $this->currentLanguage = $language;
            $manufacturers = $this->getQuery($language);
            
            foreach($manufacturers as $manufacturer) {
                $this->pushRevision($manufacturer);
            }
        }
    }
    
    
    public function exportManufacturer(int $manufacturer): void
    {
        $language = $this->languageReadService->getLanguages();
        
        foreach($language as $language) {
            $this->currentLanguage = $language;
            $this->pushRevision($this->getQuery($language, $manufacturer));
        }
    }
    
    private function pushRevision(array $manufacturer): void
    {
        $makairaManufacturer = MakairaManufacturer::mapFromManufacturer($manufacturer);
        
        $data = $this->addMakairaDocumentWrapper($makairaManufacturer);
        
        foreach($data['items'] as $itemIndex => $item) {
            $data['items'][$itemIndex]['language_id'] = $this->currentLanguage->code();
        }
        
        $this->logger->info(\GuzzleHttp\json_encode($data));
        
        $response = $this->client->push_revision($data);
        
        $this->logger->info("Makaira Manufacturer Status for " . $manufacturer['manufacturers_id'] . ": " . $response->getStatusCode());
    }
    
    
    public function getQuery(Language $language, int|null $manufacturer_id = null): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('manufacturers')
            ->leftJoin('manufacturers',
                       'manufacturers_info',
                       'manufacturers_info',
                       'manufacturers.manufacturers_id = manufacturers_info.manufacturers_id')
            ->where('manufacturers_info.languages_id = :languageId')
            ->setParameter('languageId', $language->id());
        
        if ($manufacturer_id) {
            $query->where('manufacturers.manufacturers_id = :manufacturerId')
                ->setParameter('manufacturerId', $manufacturer_id);
        }
        
        return $query->fetchAllAssociative();
    }
}