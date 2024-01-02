<?php

declare(strict_types=1);

use Gambio\Core\Application\Routing\RouteCollector;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectOverview;
use GXModules\Makaira\GambioConnect\App\Actions\ClearAllCaches;
use GXModules\Makaira\GambioConnect\App\Actions\ClearCoreCache;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/makaira/gambio-connect', GambioConnectOverview::class);

    // $routeCollector->post('/admin/gambio-samples/cache-cleaner/clear-all', ClearAllCaches::class);
    // $routeCollector->post('/admin/gambio-samples/cache-cleaner/clear-core', ClearCoreCache::class);
};
