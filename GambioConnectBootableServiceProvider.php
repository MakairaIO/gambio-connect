<?php

namespace GXModules\Makaira\GambioConnect;

use Gambio\Admin\Layout\Menu\Filter\FilterFactory;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantsStock;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use GXModules\Makaira\GambioConnect\Admin\MenuFilter\IsInstalledFilter;
use GXModules\Makaira\GambioConnect\Admin\MenuFilter\IsSetUpFilter;
use GXModules\Makaira\GambioConnect\App\EventListeners\VariantUpdateEventListener;

class GambioConnectBootableServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(FilterFactory::class)->invokeMethod('addFilter', ['isSetupFilter', IsSetUpFilter::class]);
        $this->application->inflect(FilterFactory::class)->invokeMethod('addFilter', ['isInstalledFilter', IsInstalledFilter::class]);
        $this->application->attachEventListener(UpdatedProductVariantsStock::class, VariantUpdateEventListener::class);
    }

    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {

    }
}
