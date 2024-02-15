<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\Admin\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class GambioConnectFAQ
 *
 * @package GXModules\Makaira\GambioConnect\App\Actions
 */
class GambioConnectFAQ extends AdminModuleAction
{
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $pageTitle    = 'Makaira Gambio FAQs';
        $templatePath = __DIR__ . '/../ui/template/faq.html';

        // $gxCoreLoader = \MainFactory::create(
        //     'GXCoreLoader',
        //     \MainFactory::create('GXCoreLoaderSettings')
        // );
        // $db = $gxCoreLoader->getDatabaseQueryBuilder();




        $data     = [
            'overviewJs' => "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/overview.js"
        ];
        $template = $this->render($pageTitle, $templatePath, $data);

        return $response->write($template);
    }
}
