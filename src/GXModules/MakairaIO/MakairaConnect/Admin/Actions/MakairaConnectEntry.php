<?php

declare(strict_types=1);

namespace GXModules\MakairaIO\MakairaConnect\Admin\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleStatusService;

/**s
 * Class MakairaConnectEntry
 *
 * @package GXModules\MakairaIO\MakairaConnect\Admin\Actions
 */
class MakairaConnectEntry extends AdminModuleAction
{
    public function __construct(protected ModuleStatusService $moduleStatusService) {}

    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, Response $response): Response
    {
        return $response->withRedirect($this->url->admin().'/makaira/account', 302);
    }
}
