<?php

namespace GXModules\MakairaIO\MakairaConnect\App\EventListeners;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantsStock;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;

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
