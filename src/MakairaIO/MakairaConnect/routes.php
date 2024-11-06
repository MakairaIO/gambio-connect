<?php

declare(strict_types=1);

use Gambio\Core\Application\Routing\RouteCollector;
use GXModules\MakairaIO\MakairaConnect\Admin\Actions\MakairaConnectAccount;
use GXModules\MakairaIO\MakairaConnect\Admin\Actions\MakairaConnectEntry;
use GXModules\MakairaIO\MakairaConnect\Admin\Actions\MakairaConnectManualSetup;
use GXModules\MakairaIO\MakairaConnect\App\Actions\Export;
use GXModules\MakairaIO\MakairaConnect\App\Actions\ReplaceAction;
use GXModules\MakairaIO\MakairaConnect\App\Actions\SwitchAction;

return static function (RouteCollector $routeCollector): void {
    $routeCollector->get('/admin/makaira/gambio-connect', MakairaConnectEntry::class);
    $routeCollector->get('/admin/makaira/manual-setup', MakairaConnectManualSetup::class);
    $routeCollector->post('/admin/makaira/manual-setup', MakairaConnectManualSetup::class);
    $routeCollector->get('/admin/makaira/account', MakairaConnectAccount::class);
    $routeCollector->post('/admin/makaira/account', MakairaConnectAccount::class);

    $routeCollector->post('/admin/makaira/gambio-connect/sync/export', Export::class);
    $routeCollector->post('/admin/makaira/gambio-connect/sync/replace', ReplaceAction::class);
    $routeCollector->post('/admin/makaira/gambio-connect/sync/switch', SwitchAction::class);
};
