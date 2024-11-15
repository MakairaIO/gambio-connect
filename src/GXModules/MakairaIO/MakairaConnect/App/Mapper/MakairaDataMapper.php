<?php

namespace GXModules\MakairaIO\MakairaConnect\App\Mapper;

use DateTime;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaCategory;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaEntity;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaManufacturer;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaProduct;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaVariant;

class MakairaDataMapper
{
    /**
     * @throws \Exception
     */
    public static function mapManufacturer(int $id, string $languageCode): MakairaManufacturer
    {
        $transfer = new MakairaManufacturer;
        $transfer->setType(MakairaEntity::DOC_TYPE_MANUFACTURER);

        /** @var \ManufacturerReadService $manufacturerReadService */
        $manufacturerReadService = \StaticGXCoreLoader::getService('ManufacturerRead');
        /** @var \Manufacturer $manufacturer */
        $manufacturer = $manufacturerReadService->getById(new \IdType($id));
        $transfer->setCreatedAt($manufacturer->getDateAdded())
            ->setUpdatedAt($manufacturer->getLastModified())
            ->setId($manufacturer->getId())
            ->setTitle($manufacturer->getName())
            ->setPictureUrlMain($manufacturer->getImage())
            ->setRemoteUrl($manufacturer->getUrl(new \LanguageCode(new \StringType($languageCode))));

        return $transfer;
    }

    /**
     * @throws \Exception
     */
    public static function mapCategory(int $id, bool $delete, string $language): MakairaCategory
    {
        $transfer = new MakairaCategory;

        $categoryReadService = \StaticGXCoreLoader::getService('CategoryRead');

        if($delete) {
            return $transfer->setType(MakairaCategory::DOC_TYPE_CATEGORY)
                ->setId($id)
                ->setCategoriesId($id)
                ->setDelete($delete);
        }

        /** @var \CategoryReadService $categoryReadService */
        $categoryId = new \IdType($id);
        $category = $categoryReadService->getCategoryById($categoryId);

        $languageCode = new \LanguageCode(new \StringType($language));

        $transfer
            ->setType(MakairaEntity::DOC_TYPE_CATEGORY)
            ->setId($category->getCategoryId())
            ->setCategoryTitle($categoryTitle = $category->getName($languageCode))
            ->setUrl('?' . xtc_category_link($categoryId, $categoryTitle, $language))
            ->setCategoryDescription($category->getDescription($languageCode) ?? '')
            ->setCategoryDescriptionBottom($category->getDescriptionBottom($languageCode) ?? '')
            ->setCategoryHeadingTitle($category->getHeadingTitle($languageCode) ?? '')
            ->setGmAltText($category->getImageAltText($languageCode) ?? '')
            ->setShowSubCategories($category->getSettings()->showSubcategories())
            ->setShowSubCategoriesImages($category->getSettings()->showSubcategoryImages())
            ->setShowSubCategoriesNames($category->getSettings()->showSubcategoryNames())
            ->setShowCategoriesImageInDescription($category->getSettings()->showCategoryImageInDescription())
            ->setShowSubProducts($category->getSettings()->showSubcategoryProducts())
            ->setCategoriesTemplate($category->getSettings()->getCategoryListingTemplate())
            ->setViewModeTiled($category->getSettings()->isDefaultViewModeTiled())
            ->setCategoriesImage($category->getImage())
            ->setGmShowQtyInfo($category->getSettings()->showQuantityInput());

        return $transfer;
    }

    public static function mapVariant(array $product, string $languageId, string $languageCode, array $currencyCode, array $customerStatusId, ProductVariant $variant): MakairaVariant
    {
        $product = (new \product($product['products_id'], $languageId))->data;
        $productDocument = self::mapProduct($product, $languageId, $languageCode, $currencyCode, $customerStatusId);
        $variantDocument = new MakairaVariant;
        $variantDocument->setProduct($product);
        $variantDocument->setType(MakairaEntity::DOC_TYPE_VARIANT);
        $variantDocument->setId($variant->id())
            ->setShop(1)
            ->setParent($variant->productId())
            ->setLongdesc($product['products_description'])
            ->setShortdesc($product['products_short_description'])
            ->setPrice((float)$product['products_price'])
            ->setTitle($product['products_name'])
            ->setEan($variant->ean() ?? '')
            ->setIsVariant(true)
            ->setStock($variant->stock())
            ->setOnstock($variant->stock() > 1)
            ->setMetaDescription($product['products_meta_description'])
            ->setMetaKeywords($product['products_meta_keywords'])
            ->setMaincategory($productDocument->getMainCategory())
            ->setMaincategoryurl($productDocument->getMainCategoryUrl())
            ->setPictureUrlMain($productDocument->getPictureUrlMain());

        return $variantDocument;
    }

    public static function mapProduct(array $data, string $languageId, string $languageCode, array $currencyCode, array $customerStatusId): MakairaProduct
    {
        $transfer = new MakairaProduct;
        if ($data['delete']) {
            return $transfer->setId($data['products_id'])
                ->setType(MakairaEntity::DOC_TYPE_PRODUCT)
                ->setDelete(true);
        }
        /** @var \ProductReadService $productReadService */
        $productReadService = \StaticGXCoreLoader::getService('ProductRead');
        /** @var \StoredProduct $product */
        $storedProduct = $productReadService->getProductById($data['products_id']);
        $product = new \product($data['products_id'], $languageId);
        $cooProduct = $product->buildDataArray($product->data);

        $data = $product->data;

        $data['coo_product'] = $cooProduct;

        $category = [
            'catid' => $data['main_category_id'] ?? 0,
            'shopid' => 1,
            'path' => $data['coo_product']['PRODUCTS_CATEGORY_URL'],
        ];

        $image = '';

        if (! empty($data['products_image'])) {
            $image = HTTPS_SERVER.DIR_WS_CATALOG.'images/product_images/original_images/'.$data['products_image'];
        }

        $groups = [];
        foreach ($currencyCode as $code) {
            foreach ($customerStatusId as $statusId) {
                $groups[$languageId.'_'.$code['code'].'_'.$statusId['customers_status_id']] = [
                    'products_price' => [
                        'formated' => $cooProduct['PRODUCTS_PRICE'],
                        'plain' => $data['products_price'],
                    ],
                    'products_shipping_name' => $data['coo_product']['PRODUCTS_SHIPPING_NAME'],
                    'products_shipping_range' => $data['coo_product']['PRODUCTS_SHIPPING_RANGE'],
                    'products_shipping_image' => $data['coo_product']['PRODUCTS_SHIPPING_IMAGE'],
                    'products_shipping_link_active' => $data['coo_product']['PRODUCTS_SHIPPING_LINK_ACTIVE'],
                    'coo_product' => $cooProduct,
                ];
            }
        }

        $transfer->setType(MakairaEntity::DOC_TYPE_PRODUCT)
            ->setId($storedProduct->getProductId())
            ->setStock($storedProduct->getQuantity())
            ->setPrice($storedProduct->getPrice())
            ->setIsVariant(false)
            ->setPictureUrlMain($image)
            ->setTitle($storedProduct->getName($languageCodeEntity = new \LanguageCode(new \StringType($languageCode))))
            ->setLongDescription($storedProduct->getDescription($languageCodeEntity))
            ->setShortDescription($storedProduct->getShortDescription($languageCodeEntity))
            ->setEan($storedProduct->getEan())
            ->setModel($storedProduct->getProductModel())
            ->setDateAdded($storedProduct->getAddedDateTime()->format('Y-m-d H:i:s'))
            ->setDateAvailable($storedProduct->getAvailableDateTime()->format('Y-m-d H:i:s'))
            ->setUrl($storedProduct->getInfoUrl($languageCodeEntity))
            ->setTaxClassId($storedProduct->getTaxClassId())
            ->setFsk18($storedProduct->isFsk18())
            ->setGmAltText($storedProduct->getPrimaryImage()->getAltText($languageCodeEntity))
            ->setProductsVpe($storedProduct->getVpeId())
            ->setProductsVpeStatus($storedProduct->isVpeActive())
            ->setProductsVpeValue($storedProduct->getVpeValue())
            ->setSearchKeys($storedProduct->getKeywords($languageCodeEntity))
            ->setCategories([$category])
            ->setMainCategory($category['title'] ?? '')
            ->setMainCategoryUrl($category['path'])
            ->setGroups($groups);

        return $transfer;
    }
}
