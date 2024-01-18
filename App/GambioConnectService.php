<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App;

use CategoryReadService;
use Gambio\Admin\Modules\Configuration\Services\Interfaces\CategoryRepositoryInterface;
use Gambio\Admin\Modules\Language\App\LanguageReadService;
use Gambio\Admin\Modules\Option\App\OptionReadService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionReadService;
use GXModules\Makaira\GambioConnect\Service\GambioConnectService as GambioConnectServiceInterface;
use MainFactory;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService;
use Gambio\Admin\Modules\ProductOption\App\ProductOptionReadService;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaProduct;
use Doctrine\DBAL\Connection;
use IdType;
use LanguageCode;
use ProductRepositoryReader;
use Psr\Log\LoggerInterface;
use StringType;

/**
 * Class GambioConnectService
 *
 * @package GXModules\Makaira\GambioConnect\App
 */
class GambioConnectService implements GambioConnectServiceInterface
{
    // private ProductRepositoryReader $productReadService;


    public function __construct(
        protected MakairaClient $client,
        protected LanguageReadService $languageReadService,
        protected ProductVariantsReadService $variantReadService,
        protected AdditionalOptionReadService $additionalOptionReadService,
        protected Connection $connection,
        protected MakairaLogger $logger,
        //   ProductRepositoryReader $productReadService,

    ) {
        // $this->productReadService = $productReadService;
    }

    
}
