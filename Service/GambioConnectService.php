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
    public function export(): void;
    public function replace(): void;
    public function switch(): void;
}
