<?php

namespace GXModules\Makaira\MakairaConnect\App\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\Makaira\MakairaConnect\App\GambioConnectService;

class SwitchAction extends AbstractAction
{
    public function __construct(
        protected GambioConnectService $service
    ) {
    }


    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $this->service->switch();
        return $response->withJson(['success' => true]);
    }
}
