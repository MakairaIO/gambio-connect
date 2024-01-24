<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App;

use Gambio\Admin\Modules\Language\App\LanguageReadService;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionReadService;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaEntity;
use GXModules\Makaira\GambioConnect\Service\GambioConnectService as GambioConnectServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService;
use Doctrine\DBAL\Connection;

/**
 * Class GambioConnectService
 *
 * @package GXModules\Makaira\GambioConnect\App
 */
class GambioConnectService implements GambioConnectServiceInterface
{
    // private ProductRepositoryReader $productReadService;
    
    public function __construct(
        protected MakairaClient               $client,
        protected LanguageReadService         $languageReadService,
        protected ProductVariantsReadService  $variantReadService,
        protected AdditionalOptionReadService $additionalOptionReadService,
        protected Connection                  $connection,
        protected MakairaLogger               $logger,
        //   ProductRepositoryReader $productReadService,
    
    )
    {
        // $this->productReadService = $productReadService;
    }
    
    
    public function addMakairaDocumentWrapper(MakairaEntity $document, ?Language $language = null): array
    {
        $data = [
            'items' => [
                [
                    'data' => $document->toArray(),
                ],
            ],
            'import_timestamp'  => (new \DateTime())->format('Y-m-d H:i:s'),
            'source_identifier' => 'gambio',
        ];
        
        if($language) {
            $data['items'][0]['language_id'] = $language->code();
        }
        
        $this->logger->debug('Makaira document wrapper', $data);
        
        return $data;
    }
    
    
}
