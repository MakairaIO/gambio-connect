<?php

namespace GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;

use Doctrine\DBAL\FetchMode;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaEntity;
use GXModules\MakairaIO\MakairaConnect\App\GambioConnectService;
use GXModules\MakairaIO\MakairaConnect\App\Mapper\MakairaDataMapper;
use GXModules\MakairaIO\MakairaConnect\App\Service\GambioConnectEntityInterface;

class GambioConnectProductService extends GambioConnectService implements GambioConnectEntityInterface
{
    public string $currentLanguage;

    public string $currentLanguageCode;

    protected array $currencyCodes;

    protected array $customerStatusIds;

    public static array $productRelationTables = [
        'products_attributes',
        'products_content',
        'products_description',
        'products_google_categories',
        'products_graduated_prices',
        'products_hermesoptions',
        'products_images',
        'products_item_codes',
        'products_properties_admin_select',
        'products_properties_combis',
        'products_properties_combis_defaults',
        'products_properties_index',
        'products_quantity_unit',
        'products_to_categories',
        'products_xsell',
    ];

    public function prepareExport(): void
    {
        $languages = $this->getLanguages();

        /** @var \ProductReadService $productReadService */
        $productReadService = \StaticGXCoreLoader::getService('ProductRead');


        foreach ($languages as $language) {
            $products = $productReadService->getProductList(new \LanguageCode(new \StringType($language->code())));

            /** @var \ProductListItem $product */
            foreach ($products as $product) {
                $this->connection->executeQuery('CALL makairaChange('.$product->getProductId().', "product")');
            }
        }
    }

    public function export(array $changes = []): void
    {
        $this->currencyCodes = $this->getCurrencyCodes();

        $this->customerStatusIds = $this->getCustomerStatusIds();

        $this->currentLanguage = $_SESSION['languages_id'];

        $this->currentLanguageCode = $_SESSION['language_code'];

        if (! empty($changes)) {
            $documents = [];
            foreach($changes as $change) {
                try {
                    $documents[] = $this->exportDocument($change);
                } catch (\Exception $exception) {
                    $this->logger->error('Product Export to Makaira Failed', [
                        'id' => $change['gambio_id'],
                        'message' => $exception->getMessage(),
                    ]);
                }
            }

            foreach(array_chunk($documents, 1000) as $documentChunk) {
                $data = $this->addMultipleMakairaDocuments($documentChunk, $this->currentLanguageCode);

                $this->client->pushRevision($data);
            }
        }
    }

    public function exportDocument($change): array
    {
        $this->currencyCodes = $this->getCurrencyCodes();

        $this->customerStatusIds = $this->getCustomerStatusIds();

        $this->currentLanguage = $_SESSION['languages_id'];

        $this->currentLanguageCode = $_SESSION['language_code'];

        $documents = [];

        $document = MakairaDataMapper::mapProduct((int)$change['gambio_id'], $this->currentLanguage, $this->currentLanguageCode, $this->currencyCodes, $this->customerStatusIds);
        if ($document->getId()) {
            $documents[] = $document->toArray();

            $variants =
                $this
                    ->productVariantsRepository
                    ->getProductVariantsByProductId(ProductId::create($change['gambio_id']));

            foreach ($variants as $variant) {
                $documents[] = $variantDocument = MakairaDataMapper::mapVariant(
                    (int)$change['gambio_id'],
                    $this->currentLanguage,
                    $this->currentLanguageCode,
                    $this->currencyCodes,
                    $this->customerStatusIds,
                    $variant
                )->toArray();
            }
        }
        return $documents;
    }
}
