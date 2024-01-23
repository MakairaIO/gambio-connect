<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use Exception;
use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaManufacturer;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;
use GXModules\Makaira\GambioConnect\App\Mapper\MakairaDataMapper;

class GambioConnectManufacturerService extends GambioConnectService
{
    private Language $currentLanguage;
    
    
    /**
     * @throws Exception
     */
    public function export(int $manufacturerId = null): void
    {
        if (!$manufacturerId) {
            $this->exportAll();
        } else {
            $this->exportManufacturer($manufacturerId);
        }
    }
    
    
    /**
     * @throws Exception
     */
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
    
    
    /**
     * @throws Exception
     */
    public function exportManufacturer(int $manufacturer): void
    {
        $language = $this->languageReadService->getLanguages();
        
        foreach($language as $language) {
            $this->currentLanguage = $language;
            $this->pushRevision($this->getQuery($language, $manufacturer));
        }
    }
    
    
    /**
     * @throws Exception
     */
    private function pushRevision(array $manufacturer): void
    {
        $this->logger->info("Pushing Makaira Manufacturer for " . $manufacturer['manufacturers_id']);
        
        $mapper = new MakairaDataMapper();
        $makairaManufacturer = $mapper->mapManufacturer($manufacturer, $this->currentLanguage);
        
        $this->logger->info(json_encode($makairaManufacturer));
        
        $data = $this->addMakairaDocumentWrapper($makairaManufacturer);
        $response = $this->client->push_revision($data);
        
        $this->logger->info("Makaira Manufacturer Status for " . $manufacturer['manufacturers_id'] . ": " . $response->getStatusCode());
    }
    
    public function replace(): void
    {
        $this->client->rebuild(['manufacturer']);
    }
    
    
    public function switch(): void
    {
        $this->client->rebuild(['manufacturer']);
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