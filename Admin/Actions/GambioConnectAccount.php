<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\Makaira\GambioConnect\Admin\Services\ModuleStatusService;

/**s
 * Class GambioConnectAccount
 *
 * @package GXModules\Makaira\GambioConnect\Admin\Actions
 */
class GambioConnectAccount extends AdminModuleAction
{

    private $templatePath = __DIR__ . '/../ui/template/account.html';
    private $templatePathInSetup = __DIR__ . '/../ui/template/in-setup.html';


    public function __construct(protected ModuleStatusService $moduleStatusService)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        if ($this->moduleStatusService->isInSetup()) {

            $template = $this->render(
                $this->translate('account', 'general'),
                $this->templatePathInSetup,
                []
            );

            return $response->write($template);
        }



        $template = $this->render(
            $this->translate('account', 'general'),
            $this->templatePath,
            []
        );

        return $response->write($template);
    }
}
