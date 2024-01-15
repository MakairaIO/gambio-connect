<?php

declare(strict_types=1);

use Gambio\Core\Application\Routing\RouteCollector;
use GXModules\Makaira\GambioConnect\App\Actions\Export;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectOverview;
use GXModules\Makaira\GambioConnect\App\Actions\Replace;
use GXModules\Makaira\GambioConnect\App\Actions\Switch;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/makaira/gambio-connect', GambioConnectOverview::class);
    $routeCollector->post('/admin/makaira/gambio-connect/sync/export', Export::class);
    $routeCollector->post('/admin/makaira/gambio-connect/sync/replace', Replace::class);
    //  $routeCollector->post('/admin/makaira/gambio-connect/sync/switch', Switch::class);
};
