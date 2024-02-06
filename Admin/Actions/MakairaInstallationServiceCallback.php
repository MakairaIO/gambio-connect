<?php

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectCategoryService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectManufacturerService;
use GXModules\Makaira\GambioConnect\App\GambioConnectService\GambioConnectProductService;

class MakairaInstallationServiceCallback extends AbstractAction
{
    public function __construct(
        protected GambioConnectManufacturerService $gambioConnectManufacturerService,
        protected GambioConnectCategoryService $gambioConnectCategoryService,
        protected GambioConnectProductService $gambioConnectProductService
    ) { }
   
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        
        $configurationService->save('modules/MakairaGambioConnect/makairaUrl', $request->getParsedBodyParam('url'));
        
        $configurationService->save('modules/MakairaGambioConnect/makairaInstance', $request->getParsedBodyParam('instance'));
        
        $configurationService->save('modules/MakairaGambioConnect/makairaSecret', $request->getParsedBodyParam('sharedSecret'));
       
        $this->gambioConnectManufacturerService->prepareExport();
        
        $this->gambioConnectCategoryService->prepareExport();
        
        $this->gambioConnectProductService->prepareExport();
        
        return $response->withJson(['success' => true]);
    }
}