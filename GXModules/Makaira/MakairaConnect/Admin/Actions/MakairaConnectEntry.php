<?php

declare(strict_types=1);

namespace GXModules\Makaira\MakairaConnect\Admin\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\Makaira\MakairaConnect\Admin\Services\ModuleStatusService;

/**s
 * Class MakairaConnectEntry
 *
 * @package GXModules\Makaira\MakairaConnect\Admin\Actions
 */
class MakairaConnectEntry extends AdminModuleAction
{
    public function __construct(protected ModuleStatusService $moduleStatusService)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {

        if ($this->moduleStatusService->isInSetup() || $this->moduleStatusService->isSetUp()) {
            return $response->withRedirect($this->url->admin() . '/makaira/account', 302);
        }

        return $response->withRedirect($this->url->admin() . '/makaira/welcome', 302);
    }
}
