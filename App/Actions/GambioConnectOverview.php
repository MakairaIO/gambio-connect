<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class GambioConnectOverview
 *
 * @package GXModules\Makaira\GambioConnect\App\Actions
 */
class GambioConnectOverview extends AdminModuleAction
{
    public function __construct(
        protected Application $application
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $pageTitle    = 'Makaira Gambio Connect';
        $templatePath = __DIR__ . '/../../ui/template/overview.html';

        // $gxCoreLoader = \MainFactory::create(
        //     'GXCoreLoader',
        //     \MainFactory::create('GXCoreLoaderSettings')
        // );
        // $db = $gxCoreLoader->getDatabaseQueryBuilder();




        $data     = [
            'overviewJs' => "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/overview.js",
            'http_url' => $this->url->base(),
            'http_admin_url' => $this->url->admin(),
            'subdomain' => str_replace(['http://', '.'], ['','-'], $this->url->base())
        ];
        $template = $this->render($pageTitle, $templatePath, $data);

        return $response->write($template);
    }
}
