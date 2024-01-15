<?php


declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;

/**
 * Class Export
 *
 * @package GXModules\Makaira\GambioConnect\App\Actions
 */
class Export extends AbstractAction
{
    /**
     * @var GambioConnectService
     */
    private $service;



    public function __construct(GambioConnectService $service)
    {
        $this->service = $service;
    }


    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $this->service->export();
        return $response->withJson(['success' => true]);
    }
}
