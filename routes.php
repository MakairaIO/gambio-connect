<?php

declare(strict_types=1);

use Gambio\Core\Application\Routing\RouteCollector;
use GXModules\Makaira\GambioConnect\App\Actions\Export;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectAccount;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectDocument;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectFAQ;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectWelcome;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectOverview;
use GXModules\Makaira\GambioConnect\App\Actions\ReplaceAction;
use GXModules\Makaira\GambioConnect\App\Actions\SwitchAction;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/makaira/gambio-connect', GambioConnectOverview::class);
    $routeCollector->get('/admin/makaira/welcome', GambioConnectWelcome::class);
    $routeCollector->get('/admin/makaira/faq', GambioConnectFAQ::class);
    $routeCollector->get('/admin/makaira/document', GambioConnectDocument::class);
    $routeCollector->get('/admin/makaira/account', GambioConnectAccount::class);

    $routeCollector->post('/admin/makaira/gambio-connect/sync/export', Export::class);
    $routeCollector->post('/admin/makaira/gambio-connect/sync/replace', ReplaceAction::class);
    $routeCollector->post('/admin/makaira/gambio-connect/sync/switch', SwitchAction::class);
};
