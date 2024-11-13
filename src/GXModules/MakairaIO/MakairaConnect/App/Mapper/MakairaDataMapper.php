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
    public static function mapManufacturer(array $data): MakairaManufacturer
    {
        $transfer = new MakairaManufacturer;

        if ($data['delete']) {
            return $transfer->setType(MakairaEntity::DOC_TYPE_MANUFACTURER)
                ->setId($data['manufacturers_id'])
                ->setDelete(true);
        }

        $createdAt = $data['date_added'] ? new DateTime($data['date_added']) : null;
        $updatedAt = $data['last_modified'] ? new DateTime($data['last_modified']) : null;
        $lastClickedAt = $data['date_last_click'] ? new DateTime($data['date_last_click']) : null;

        $transfer
            ->setType(MakairaEntity::DOC_TYPE_MANUFACTURER)
            ->setId($data['manufacturers_id'])
            ->setTitle($data['manufacturers_name'])
            ->setPictureUrlMain($data['manufacturers_image'])
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt)
            ->setMetaTitle($data['manufacturers_meta_title'])
            ->setMetaDescription($data['manufacturers_meta_description'])
            ->setMetaKeywords($data['manufacturers_meta_keywords'])
            ->setRemoteUrl($data['manufacturers_url'])
            ->setIsUrlClicked($data['url_clicked'])
            ->setLastClickedAt($lastClickedAt);

        return $transfer;
    }

    /**
     * @throws \Exception
     */
    public static function mapCategory(array $data, array $hierarchy, string $language): MakairaCategory
    {
        $transfer = new MakairaCategory;

        if ($data['delete']) {
            return $transfer->setType(MakairaEntity::DOC_TYPE_CATEGORY)
                ->setId($data['categories_id'])
                ->setCategoriesId($data['categories_id'])
                ->setDelete(true);
        }

        $subCategories = [];

        foreach ($data['subcategories'] as $subcategory) {
            $subCategories[] = $subcategory['categories_id'];
        }

        $transfer
            ->setType(MakairaEntity::DOC_TYPE_CATEGORY)
            ->setId($data['categories_id'])
            ->setCategoryTitle($data['categories_name'])
            ->setDepth($hierarchy['depth'])
            ->setHierarchy($hierarchy['hierarchy'])
            ->setSubCategories($subCategories)
            ->setUrl('?'.xtc_category_link($data['categories_id'], $data['categories_name'], $language))
            ->setCategoryDescription($data['categories_description'] ?? '')
            ->setCategoryDescriptionBottom($data['categories_description_bottom'] ?? '')
            ->setCategoryHeadingTitle($data['categories_heading_title'] ?? '')
            ->setGmAltText($data['gm_alt_text'] ?? '')
            ->setShowSubCategories($data['show_sub_categories'] ?? false)
            ->setShowSubCategoriesImages($data['show_sub_categories_images'] ?? false)
            ->setShowSubCategoriesNames($data['show_sub_categories_names'] ?? false)
            ->setShowCategoriesImageInDescription($data['show_sub_categories_image_in_description'] ?? false)
            ->setShowSubProducts($data['show_sub_products'] ?? false)
            ->setCategoriesTemplate($data['categories_template'] ?? '')
            ->setCategoriesId($data['categories_id'])
            ->setViewModeTiled($data['view_mode_tiled'])
            ->setCategoriesImage($data['categories_image'] ?? '')
            ->setGmShowQtyInfo($data['gm_show_qty_info']);

        return $transfer;
    }

    public static function mapVariant(array $product, string $languageId, array $currencyCode, array $customerStatusId, ProductVariant $variant): MakairaVariant
    {
        $product = (new \product($product['products_id'], $languageId))->data;
        $productDocument = self::mapProduct($product, $languageId, $currencyCode, $customerStatusId);
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

    public static function mapProduct(array $data, string $languageId, array $currencyCode, array $customerStatusId): MakairaProduct
    {
        $transfer = new MakairaProduct;
        if ($data['delete']) {
            return $transfer->setId($data['products_id'])
                ->setType(MakairaEntity::DOC_TYPE_PRODUCT)
                ->setDelete(true);
        }
        $product = new \product($data['products_id'], $languageId);
        $cooProduct = $product->buildDataArray($product->data);

        $data = $product->data;

        $data['coo_product'] = $cooProduct;

        $stock = 1;

        $category = [
            'catid' => $data['main_category_id'],
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
            ->setId($data['products_id'])
            ->setStock($stock)
            ->setPrice((float)$data['products_price'])
            ->setIsVariant(false)
            ->setPictureUrlMain($image)
            ->setTitle($data['products_name'])
            ->setLongDescription($data['products_description'])
            ->setShortDescription($data['products_short_description'])
            ->setEan($data['products_ean'] ?? '')
            ->setMpn($data['products_item_codes']['code_mpn'] ?? '')
            ->setIsbn($data['products_item_codes']['code_isbn'] ?? '')
            ->setUpc($data['products_item_codes']['code_upc'] ?? '')
            ->setJan($data['products_item_codes']['code_jan'] ?? '')
            ->setModel($data['products_model'])
            ->setDateAdded($data['products_date_added'] ?? '')
            ->setDateAvailable($data['products_date_available'] ?? '')
            ->setUrl($data['coo_product']['PRODUCTS_LINK'])
            ->setTaxClassId((int)$data['products_tax_class_id'])
            ->setFsk18((bool) $data['products_fsk18'] ?? false)
            ->setGmAltText($data['gm_alt_text'] ?? '')
            ->setProductsVpe((int)$data['products_vpe'])
            ->setProductsVpeStatus((int)$data['products_vpe_status'])
            ->setProductsVpeValue((int)$data['products_vpe_value'])
            ->setSearchKeys($data['products_keywords'] ?? '')
            ->setCategories([$category])
            ->setMainCategory($category['title'] ?? '')
            ->setMainCategoryUrl($category['path'])
            ->setGroups($groups);

        return $transfer;
    }
}
