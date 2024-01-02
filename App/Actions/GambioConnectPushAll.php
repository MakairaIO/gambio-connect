<?php
/* --------------------------------------------------------------
 ClearCoreCache.php 2020-08-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace GXModules\GambioSamples\CacheCleaner\App\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\GambioSamples\CacheCleaner\Service\CacheCleanerService;

/**
 * Class ClearCoreCache
 *
 * @package GXModules\GambioSamples\CacheCleaner\App\Actions
 */
class ClearCoreCache extends AbstractAction
{
    /**
     * @var CacheCleanerService
     */
    private $service;
    
    
    /**
     * @param CacheCleanerService $service
     */
    public function __construct(CacheCleanerService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $this->service->clearCore();
        
        return $response->withJson(['success' => true]);
    }
}