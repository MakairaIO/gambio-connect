<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect;

use Gambio\Core\Application\DependencyInjection\AbstractModuleServiceProvider;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Cache\Services\CacheFactory;
use GXModules\GambioSamples\CacheCleaner\App\Actions\ClearAllCaches;
use GXModules\GambioSamples\CacheCleaner\App\Actions\ClearCoreCache;
use GXModules\GambioSamples\CacheCleaner\Service\CacheCleanerService;
use GXModules\Makaira\GambioConnect\App\Actions\GambioConnectOverview;

/**
 * Class GambioConnectServiceProvider
 *
 * @package GXModules\Makaira\GambioConnect
 */
class GambioConnectServiceProvider extends AbstractModuleServiceProvider
{
    /**
     * @inheritcDoc
     */
    public function provides(): array
    {
        return [
            GambioConnectOverview::class,
            // ClearAllCaches::class,
            // ClearCoreCache::class,
        ];
    }


    /**
     * @inheritcDoc
     */
    public function register(): void
    {
        $this->application->registerShared(GambioConnectOverview::class);
        // $this->application->registerShared(ClearAllCaches::class)->addArgument(CacheCleanerService::class);
        // $this->application->registerShared(ClearCoreCache::class)->addArgument(CacheCleanerService::class);

        // $this->application->registerShared(CacheCleanerService::class, App\CacheCleanerService::class)
        //     ->addArguments([CacheFactory::class, Path::class]);
    }
}
