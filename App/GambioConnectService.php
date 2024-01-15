<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App;

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
use StringType;

/**
 * Class GambioConnectService
 *
 * @package GXModules\Makaira\GambioConnect\App
 */
class GambioConnectService implements GambioConnectServiceInterface
{
    private MakairaClient $client;
    private MakairaLogger $logger;
    private ProductVariantsReadService $variantReadService;
    private AdditionalOptionReadService $additionalOptionReadService;
    private Connection $connection;
    // private ProductRepositoryReader $productReadService;


    public function __construct(
        MakairaClient $client,
        ProductVariantsReadService $variantReadService,
        AdditionalOptionReadService $additionalOptionReadService,
        Connection $connection,
        MakairaLogger $logger,
        //   ProductRepositoryReader $productReadService,

    ) {
        $this->logger = $logger;
        $this->client = $client;
        $this->variantReadService = $variantReadService;
        $this->additionalOptionReadService = $additionalOptionReadService;
        $this->connection = $connection;
        // $this->productReadService = $productReadService;
    }

    public function export(int $productId = null): void
    {

        $lang = 2;
        if ($productId === null) {
            $this->exportAll();
        } else {

            $product = $this->connection->fetchAllAssociative(
                '
                SELECT p.*, pd.products_name, pd.products_name, pd.products_description, pd.products_short_description, pd.products_url, pd.products_viewed
                  FROM ' . 'products' . ' p
             LEFT JOIN ' . 'products_description' . ' pd ON pd.products_id = p.products_id AND pd.language_id = "' . $lang . '"
             WHERE p.products_id ="' . $productId . '"'
            );


            $specificProductVariants = $this->variantReadService->getProductVariantsByProductId($productId);
            $document = new MakairaProduct($product[0], $specificProductVariants);
            $this->client->push_revision($document->addMakairaDocumentWrapper());
        }
    }



    public function exportAll(): void
    {

        $lang = 2;

        $products = $this->connection->fetchAllAssociative(
            '
            SELECT p.*, pd.products_name, pd.products_description, pd.products_short_description, pd.products_url, pd.products_viewed
			  FROM ' . 'products' . ' p
		 LEFT JOIN ' . 'products_description' . ' pd ON pd.products_id = p.products_id AND pd.language_id = "' . $lang . '"
         '
        );

        $this->logger->info(json_encode($products));



        foreach ($products as $product) {
            $specificProductVariants = $this->variantReadService->getProductVariantsByProductId((int) $product['products_id']);
            $document = new MakairaProduct($product, $specificProductVariants);
            $this->client->push_revision($document->addMakairaDocumentWrapper());

            $this->logger->info(json_encode($document->addMakairaDocumentWrapper()));
        }

        // $specificProductOption = $this->additionalOptionReadService->getAdditionalOptionsByProductId($productid);
        //  $this->logger->info(json_encode($prod));
        //$this->logger->info(json_encode($product->getName(new LanguageCode(new StringType('de')))));
        //$this->logger->info(json_encode($document->add_makaira_document_wrapper()));
    }

    public function exportByVariantId(int $variantId): void
    {
        $specificProductVariants = $this->variantReadService->getProductVariantById($variantId);
        $this->export($specificProductVariants->productId());
    }

    public function replace(): void
    {
        $this->client->rebuild(['products']);
    }

    public function switch(): void
    {
        $this->client->switch(['products']);
    }
}
