<?php

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;

class MakairaInstallationServiceCallback extends AbstractAction
{

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        
        $configurationService->save('modules/MakairaGambioConnect/makairaUrl', $request->getParsedBodyParam('url'));
        
        $configurationService->save('modules/MakairaGambioConnect/makairaInstance', $request->getParsedBodyParam('instance'));
        
        $configurationService->save('modules/MakairaGambioConnect/makairaSecret', $request->getParsedBodyParam('secret'));
        
        return $response->withJson(['success' => true]);
    }
}