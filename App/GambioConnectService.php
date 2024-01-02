<?php
/* --------------------------------------------------------------
 CacheCleanerService.php 2020-08-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App;

use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Cache\Services\CacheFactory;
use GXModules\Makaira\GambioConnect\Service\GambioConnectorService as GambioConnectServiceInterface;
use Psr\SimpleCache\InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Class GambioConnectService
 *
 * @package GXModules\Makaira\GambioConnect\App
 */
class GambioConnectService implements GambioConnectServiceInterface
{
    /**
     * @var CacheFactory
     */
    private $cacheFactory;

    /**
     * @var Path
     */
    private $path;


    /**
     * @param CacheFactory $cacheFactory
     * @param Path         $path
     */
    public function __construct(CacheFactory $cacheFactory, Path $path)
    {
        $this->cacheFactory = $cacheFactory;
        $this->path         = $path;
    }


    /**
     * @inheritDoc
     */
    public function clearAll(): void
    {
        $this->clearCacheDirectory();
    }


    /**
     * @inheritDoc
     */
    public function clearCore(): void
    {
        try {
            $coreCache = $this->cacheFactory->createCacheFor('core');
            $coreCache->clear();
        } catch (InvalidArgumentException $e) {
        }
    }


    private function clearCacheDirectory(): void
    {
        $excludes           = [
            '.htaccess',
            'index.html',
        ];
        $cacheDir           = "{$this->path->base()}/cache";
        $validationCallback = static function (SplFileInfo $file) use ($excludes): bool {
            return !in_array($file->getFilename(), $excludes, true)
                && strpos(
                    $file->getPathname(),
                    '/sessions/'
                ) === false;
        };

        $this->deleteRecursive($cacheDir, $validationCallback);
    }


    /**
     * @param string        $path
     * @param callable|null $validationCallback
     */
    private function deleteRecursive(string $path, callable $validationCallback = null): void
    {
        foreach ($this->iterator($path) as $file) {
            $condition = $validationCallback ? $file->isFile() && $validationCallback($file) : $file->isFile();

            if ($condition) {
                @unlink($file->getPathname());
            }
        }
    }


    /**
     * @param string $path
     *
     * @return RecursiveIteratorIterator
     */
    private function iterator(string $path): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $path,
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );
    }
}
