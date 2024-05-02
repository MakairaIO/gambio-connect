<?php

declare(strict_types=1);

use Gambio\Core\Application\Routing\RouteCollector;
use GXModules\Makaira\GambioConnect\Admin\Actions\MakairaConnectAccount;
use GXModules\Makaira\GambioConnect\Admin\Actions\MakairaConnectEntry;
use GXModules\Makaira\GambioConnect\Admin\Actions\MakairaConnectManualSetup;
use GXModules\Makaira\GambioConnect\Admin\Actions\MakairaConnectWelcome;
use GXModules\Makaira\GambioConnect\Admin\Actions\MakairaCheckoutAction;
use GXModules\Makaira\GambioConnect\Admin\Actions\StripeCheckoutCancelCallback;
use GXModules\Makaira\GambioConnect\Admin\Actions\StripeCheckoutSuccessCallback;
use GXModules\Makaira\GambioConnect\App\Actions\Export;
use GXModules\Makaira\GambioConnect\App\Actions\ReplaceAction;
use GXModules\Makaira\GambioConnect\App\Actions\SwitchAction;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/makaira/gambio-connect', MakairaConnectEntry::class);
    $routeCollector->get('/admin/makaira/welcome', MakairaConnectWelcome::class);
    $routeCollector->get('/admin/makaira/manual-setup', MakairaConnectManualSetup::class);
    $routeCollector->post('/admin/makaira/manual-setup', MakairaConnectManualSetup::class);
    $routeCollector->get('/admin/makaira/account', MakairaConnectAccount::class);
    $routeCollector->post('/admin/makaira/account', MakairaConnectAccount::class);

    $routeCollector->post('/admin/makaira/gambio-connect/stripe-checkout', MakairaCheckoutAction::class);

    $routeCollector->get('/admin/makaira/gambio-connect/stripe-checkout-success-callback', StripeCheckoutSuccessCallback::class);
    $routeCollector->get('/admin/makaira/gambio-connect/stripe-checkout-cancel-callback', StripeCheckoutCancelCallback::class);

    $routeCollector->post('/admin/makaira/gambio-connect/sync/export', Export::class);
    $routeCollector->post('/admin/makaira/gambio-connect/sync/replace', ReplaceAction::class);
    $routeCollector->post('/admin/makaira/gambio-connect/sync/switch', SwitchAction::class);
};
