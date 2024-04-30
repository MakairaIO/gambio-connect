<?php

namespace GXModules\Makaira\GambioConnect\App\EventListeners;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantsStock;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;

class VariantUpdateEventListener
{
    /**
     * @var GambioConnectService
     */
    private $service;



    public function __construct(GambioConnectService $service)
    {
        $this->service = $service;
    }

    /**
     * @param AdditionalOptionsStockUpdated $event
     */
    public function __invoke(UpdatedProductVariantsStock $event): void
    {
        $this->service->exportByVariantId($event->variantId()->value());
    }
}
