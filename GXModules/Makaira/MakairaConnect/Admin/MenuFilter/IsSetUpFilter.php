<?php

namespace GXModules\Makaira\MakairaConnect\Admin\MenuFilter;

use Gambio\Admin\Layout\Menu\Filter\FilterConditionArguments;
use Gambio\Admin\Layout\Menu\Filter\FilterInterface;
use GXModules\Makaira\MakairaConnect\Admin\Services\ModuleStatusService;
use GXModules\Makaira\MakairaConnect\App\MakairaLogger;

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
