<?php

namespace GXModules\MakairaIO\MakairaConnect\App\Mapper;

use DateTime;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaCategory;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaEntity;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaManufacturer;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaProduct;
use GXModules\MakairaIO\MakairaConnect\App\Documents\MakairaVariant;
use JetBrains\PhpStorm\Language;

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
        try {
            $manufacturer = $manufacturerReadService->getById(new \IdType($id));
        } catch(\EntityNotFoundException $exception) {
            $transfer->delete();
            return $transfer;
        }

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
    public static function mapCategory(int $id, string $language): MakairaCategory
    {
        $transfer = new MakairaCategory;

        $categoryReadService = \StaticGXCoreLoader::getService('CategoryRead');

        /** @var \CategoryReadService $categoryReadService */
        $categoryId = new \IdType($id);
        try {
            $category = $categoryReadService->getCategoryById($categoryId);
        }catch (\UnexpectedValueException $exception) {
            $transfer->setId($categoryId)
                ->setCategoriesId($categoryId)
                ->delete();

            return $transfer;
        }

        $languageCode = new \LanguageCode(new \StringType($language));

        $transfer
            ->setType(MakairaEntity::DOC_TYPE_CATEGORY)
            ->setId($category->getCategoryId())
            ->setCategoriesId($categoryId)
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

    public static function mapVariant(int $productId, string $languageId, string $languageCode, array $currencyCode, array $customerStatusId, ProductVariant $variant): MakairaVariant
    {
        $product = new \product($productId, $languageId);
        $variantCooProduct = $product->buildDataArray($product->data);
        $productDocument = self::mapProduct($productId, $languageId, $languageCode, $currencyCode, $customerStatusId);
        $variantDocument = new MakairaVariant;
        $variantDocument->setProduct($variantCooProduct);
        $variantDocument->setType(MakairaEntity::DOC_TYPE_VARIANT);
        $variantDocument->setId($variant->id())
            ->setShop(1)
            ->setParent($variant->productId())
            ->setLongdesc($productDocument->getLongDescription())
            ->setShortdesc($productDocument->getShortDescription())
            ->setPrice((float)$variantCooProduct['PRODUCTS_PRICE'])
            ->setTitle($productDocument->getTitle())
            ->setEan($variant->ean() ?? '')
            ->setIsVariant(true)
            ->setStock($variant->stock())
            ->setOnstock($variant->stock() > 1)
            ->setMetaDescription($variantCooProduct['PRODUCTS_META_DESCRIPTION'] ?? '')
            ->setMetaKeywords($variantCooProduct['PRODUCTS_META_KEYWORDS'] ?? '')
            ->setMaincategory($productDocument->getMainCategory())
            ->setMaincategoryurl($productDocument->getMainCategoryUrl())
            ->setPictureUrlMain($productDocument->getPictureUrlMain());

        return $variantDocument;
    }

    public static function mapProduct(int $id, string $languageId, string $languageCode, array $currencyCode, array $customerStatusId): MakairaProduct
    {
        $transfer = new MakairaProduct;
        /** @var \ProductReadService $productReadService */
        $productReadService = \StaticGXCoreLoader::getService('ProductRead');

        /** @var \CategoryReadService $categoryReadService */
        $categoryReadService = \StaticGXCoreLoader::getService('CategoryRead');
        /** @var \StoredProduct $product */
        try {
            $storedProduct = $productReadService->getProductById(new \IdType($id));
        }catch( \UnexpectedValueException $exception) {
            return $transfer->setId($id)
                ->setType(MakairaEntity::DOC_TYPE_PRODUCT)
                ->delete();
        }
        $product = new \product($id, $languageId);
        $cooProduct = $product->buildDataArray($product->data);

        $data = $product->data;

        $data['coo_product'] = serialize($cooProduct);

        try {
            /** @var \StoredCategory $storedCategory */
            $storedCategory = $categoryReadService->getCategoryById(new \IdType($storedProduct->getMainCategoryId()));
        }catch(\UnexpectedValueException $exception) {
            $storedCategory = null;
        }

        $categoryPath = '';

        try {
            if($storedCategory) {
                $categoryPath = $storedCategory->getUrlRewrite(new \LanguageCode(new \StringType($languageCode)))->getRewriteUrl();
            }

        }catch( \InvalidArgumentException $exception) {
        }

        $category = [
            'catid' => $storedProduct->getMainCategoryId(),
            'shopid' => 1,
            'path' => $categoryPath,
        ];

        $image = '';

        $primaryImage = $storedProduct->getPrimaryImage();

        if (! empty($primaryImage->isVisible())) {
            $image = HTTPS_SERVER.DIR_WS_CATALOG.'images/product_images/original_images/' . $primaryImage->getFilename();
        }

        $groups = [];
        foreach ($currencyCode as $code) {
            foreach ($customerStatusId as $statusId) {
                $groups[$languageId.'_'.$code['code'].'_'.$statusId['customers_status_id']] = [
                    'products_price' => [
                        'formated' => $cooProduct['PRODUCTS_PRICE'],
                        'plain' => $data['products_price'],
                    ],
                    'products_shipping_name' => $cooProduct['PRODUCTS_SHIPPING_NAME'],
                    'products_shipping_range' => $cooProduct['PRODUCTS_SHIPPING_RANGE'],
                    'products_shipping_image' => $cooProduct['PRODUCTS_SHIPPING_IMAGE'],
                    'products_shipping_link_active' => $cooProduct['PRODUCTS_SHIPPING_LINK_ACTIVE'],
                    'coo_product' => $data['coo_product'],
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
            ->setIsbn($storedProduct->getAddonValues()->keyExists('codeIsbn') ? $storedProduct->getAddonValues()->getValue('codeIsbn') : '')
            ->setUpc($storedProduct->getAddonValues()->keyExists('codeUpc') ? $storedProduct->getAddonValues()->getValue('codeUpc') : '')
            ->setMpn($storedProduct->getAddonValues()->keyExists('codeMpn') ? $storedProduct->getAddonValues()->getValue('codeMpn') : '')
            ->setJan($storedProduct->getAddonValues()->keyExists('codeJan') ? $storedProduct->getAddonValues()->getValue('codeJan') : '')
            ->setModel($storedProduct->getProductModel())
            ->setDateAdded($storedProduct->getAddedDateTime()->format('Y-m-d H:i:s'))
            ->setDateAvailable($storedProduct->getAvailableDateTime()->format('Y-m-d H:i:s'))
            ->setUrl($cooProduct['PRODUCTS_LINK'])
            ->setTaxClassId($storedProduct->getTaxClassId())
            ->setFsk18($storedProduct->isFsk18())
            ->setGmAltText($storedProduct->getPrimaryImage()->getAltText($languageCodeEntity))
            ->setProductsVpe($storedProduct->getVpeId())
            ->setProductsVpeStatus($storedProduct->isVpeActive())
            ->setProductsVpeValue($storedProduct->getVpeValue())
            ->setSearchKeys($storedProduct->getKeywords($languageCodeEntity))
            ->setCategories([$category])
            ->setMainCategory($storedCategory?->getName(new \LanguageCode(new \StringType($languageCode))) ?? '')
            ->setMainCategoryUrl($category['path'])
            ->setGroups($groups);

        return $transfer;
    }
}
