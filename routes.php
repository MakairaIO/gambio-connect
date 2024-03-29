<?php

declare(strict_types=1);

use Gambio\Core\Application\Routing\RouteCollector;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectAccount;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectDocument;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectEntry;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectFAQ;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectManualSetup;
use GXModules\Makaira\GambioConnect\Admin\Actions\GambioConnectWelcome;
use GXModules\Makaira\GambioConnect\Admin\Actions\MakairaCheckoutAction;
use GXModules\Makaira\GambioConnect\Admin\Actions\MakairaInstallationServiceCallback;
use GXModules\Makaira\GambioConnect\Admin\Actions\StripeCheckoutCancelCallback;
use GXModules\Makaira\GambioConnect\Admin\Actions\StripeCheckoutSuccessCallback;
use GXModules\Makaira\GambioConnect\App\Actions\Export;
use GXModules\Makaira\GambioConnect\App\Actions\ReplaceAction;
use GXModules\Makaira\GambioConnect\App\Actions\SwitchAction;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/makaira/gambio-connect', GambioConnectEntry::class);
    $routeCollector->get('/admin/makaira/welcome', GambioConnectWelcome::class);
    $routeCollector->get('/admin/makaira/manual-setup', GambioConnectManualSetup::class);
    $routeCollector->post('/admin/makaira/manual-setup', GambioConnectManualSetup::class);
    $routeCollector->get('/admin/makaira/faq', GambioConnectFAQ::class);
    $routeCollector->get('/admin/makaira/document', GambioConnectDocument::class);
    $routeCollector->get('/admin/makaira/account', GambioConnectAccount::class);
    $routeCollector->post('/admin/makaira/account', GambioConnectAccount::class);

    $routeCollector->post('/admin/makaira/gambio-connect/stripe-checkout', MakairaCheckoutAction::class);

    $routeCollector->get('/admin/makaira/gambio-connect/stripe-checkout-success-callback', StripeCheckoutSuccessCallback::class);
    $routeCollector->get('/admin/makaira/gambio-connect/stripe-checkout-cancel-callback', StripeCheckoutCancelCallback::class);

    $routeCollector->post('/admin/makaira/gambio-connect/sync/export', Export::class);
    $routeCollector->post('/admin/makaira/gambio-connect/sync/replace', ReplaceAction::class);
    $routeCollector->post('/admin/makaira/gambio-connect/sync/switch', SwitchAction::class);
};
