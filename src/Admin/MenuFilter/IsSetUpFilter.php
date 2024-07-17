<?php

namespace GXModules\MakairaIO\MakairaConnect\Admin\MenuFilter;

use Gambio\Admin\Layout\Menu\Filter\FilterConditionArguments;
use Gambio\Admin\Layout\Menu\Filter\FilterInterface;
use GXModules\MakairaIO\MakairaConnect\Admin\Services\ModuleStatusService;
use GXModules\MakairaIO\MakairaConnect\App\MakairaLogger;

class IsSetUpFilter implements FilterInterface
{
    public function __construct(protected ModuleStatusService $moduleStatusService)
    {
    }

    public function check(FilterConditionArguments $condition): bool
    {
        $args = $condition->args();
        $isSetUp = (bool) $this->moduleStatusService->isSetUp();

        if ($args[0] == false) {
            return !$isSetUp;
        }

        return $isSetUp;
    }
}
