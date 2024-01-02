<?php
/* --------------------------------------------------------------
 CacheCleanerOverview.php 2020-08-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class GambioConnectOverview
 *
 * @package GXModules\Makaira\GambioConnect\App\Actions
 */
class GambioConnectOverview extends AdminModuleAction
{
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $pageTitle    = 'Makaira Gambio Connect';
        $templatePath = __DIR__ . '/../../ui/template/overview.html';

        $data     = [
            'overviewJs' => "{$this->url->base()}/GXModules/Makaira/GambioConnect/ui/assets/overview.js"
        ];
        $template = $this->render($pageTitle, $templatePath, $data);

        return $response->write($template);
    }
}
