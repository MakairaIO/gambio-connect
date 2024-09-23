<?php

namespace GXModules\MakairaIO\MakairaConnect\Admin\MenuFilter;

use Gambio\Admin\Layout\Menu\Filter\FilterConditionArguments;
use Gambio\Admin\Layout\Menu\Filter\FilterInterface;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleStatusService;

class IsInstalledFilter implements FilterInterface
{
    public function __construct(protected ModuleStatusService $moduleStatusService) {}

    public function check(FilterConditionArguments $condition): bool
    {
        return $this->moduleStatusService->isInstalled();
    }
}
