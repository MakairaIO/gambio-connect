<?php

namespace GXModules\Makaira\GambioConnect\App\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;

class ReplaceAction extends AbstractAction
{
    
    public function __construct(
        protected GambioConnectService $service
    ) { }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $this->service->replace();
        
        return $response->withJson(['success' => true]);
    }
}