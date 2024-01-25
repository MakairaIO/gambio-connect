<?php

use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;

class GambioConnectCronjobTask extends AbstractCronjobTask
{
    protected GambioConnectManufacturerService $gambioConnectManufacturerService;
    protected GambioConnectCategoryService     $gambioConnectCategoryService;
    protected GambioConnectProductService      $gambioConnectProductService;
    
    
    public function getCallback($cronjobStartAsMicrotime): \Closure
    {
        $dependencies = $this->dependencies->getDependencies();
        
        $this->gambioConnectManufacturerService = new GambioConnectManufacturerService(
            $dependencies['MakairaClient'],
            $dependencies['LanguageReadService'],
            $dependencies['Connection'],
            $dependencies['MakairaLogger']
        );
        
        $this->gambioConnectCategoryService = new GambioConnectCategoryService(
            $dependencies['MakairaClient'],
            $dependencies['LanguageReadService'],
            $dependencies['Connection'],
            $dependencies['MakairaLogger']
        );
        
        $this->gambioConnectProductService = new GambioConnectProductService(
            $dependencies['MakairaClient'],
            $dependencies['LanguageReadService'],
            $dependencies['Connection'],
            $dependencies['MakairaLogger']
        );
        
        return function () {
            $this->logInfo('GambioConnect Cronjob Started');
            
            $this->logInfo('Begin Export Manufacturers to PersistenceLayer');
            
            $this->gambioConnectManufacturerService->exportAll();
            
            $this->logInfo('Begin Export Categories to PersistenceLayer');
            
            $this->gambioConnectCategoryService->exportAll();
            
            $this->logInfo('Begin Export Products to PersistenceLayer');
            
            $this->gambioConnectProductService->exportAll();
            
            $this->logInfo('All Exports to PersistenceLayer Successful');
        };
    }
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    protected function logInfo(string $message): void
    {
        $this->logger->log(['message' => $message, 'level' => 'info']);
    }
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    protected function logError(string $message): void
    {
        $this->logger->logError(['message' => $message, 'level' => 'error']);
    }
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    protected function logNotice(string $message): void
    {
        $this->logger->log(['message' => $message, 'level' => 'notice']);
    }
}