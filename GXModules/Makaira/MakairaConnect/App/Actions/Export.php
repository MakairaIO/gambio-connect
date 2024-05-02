<?php

declare(strict_types=1);

namespace GXModules\Makaira\MakairaConnect\App\Actions;

use Exception;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\Makaira\MakairaConnect\App\GambioConnectService;
use GXModules\Makaira\MakairaConnect\MakairaConnectInstaller;

/**
 * Class Export
 *
 * @package GXModules\Makaira\MakairaConnect\App\Actions
 */
class Export extends AbstractAction
{
    public function __construct(
        protected GambioConnectService\GambioConnectCategoryService $gambioConnectCategoryService,
        protected GambioConnectService\GambioConnectProductService $gambioConnectProductService,
        protected GambioConnectService\GambioConnectManufacturerService $gambioConnectManufacturerService,
    ) {
    }


    /**
     * @inheritDoc
     * @throws Exception
     */
    public function handle(Request $request, Response $response): Response
    {
        $this->gambioConnectManufacturerService->prepareExport();

        $this->gambioConnectCategoryService->prepareExport();

        $this->gambioConnectProductService->prepareExport();

        return $response->withJson(['success' => true]);
    }
}
