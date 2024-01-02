<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\Service;

/**
 * Interface GambioConnectService
 *
 * @package GXModules\Makaira\GambioConnect\Service
 */
interface GambioConnectService
{
    /**
     * Clears all of the cache data.
     */
    public function clearAll(): void;


    /**
     * Clears the core cache files.
     *
     * The core cache files contains information like service providers and registered external modules.
     */
    public function clearCore(): void;
}
