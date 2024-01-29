<?php

namespace GXModules\Makaira\GambioConnect\App\Mapper;

use DateTime;
use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaCategory;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaEntity;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaManufacturer;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaProduct;

class MakairaDataMapper
{
    /**
     * @throws \Exception
     */
    public static function mapManufacturer(array $data): MakairaManufacturer
    {
        $transfer = new MakairaManufacturer();

        $createdAt = $data['date_added'] ? new DateTime($data['date_added']) : null;
        $updatedAt = $data['last_modified'] ? new DateTime($data['last_modified']) : null;
        $lastClickedAt = $data['date_last_click'] ? new DateTime($data['date_last_click']) : null;

        $transfer
            ->setType(MakairaEntity::DOC_TYPE_MANUFACTURER)
            ->setId($data['manufacturers_id'])
            ->setManufacturerTitle($data['manufacturers_name'])
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
    public static function mapCategory(array $data, array $hierarchy): MakairaCategory
    {
        $transfer = new MakairaCategory();

        $transfer
            ->setType(MakairaEntity::DOC_TYPE_CATEGORY)
            ->setId($data['categories_id'])
            ->setCategoryTitle($data['categories_name'])
            ->setDepth($hierarchy['depth'])
            ->setHierarchy($hierarchy['hierarchy']);

        return $transfer;
    }

    public static function mapProduct(array $data): MakairaProduct
    {
        $transfer = new MakairaProduct();

        $stock = 1;

        $transfer->setType(MakairaEntity::DOC_TYPE_PRODUCT)
            ->setId($data['products_id'])
            ->setStock($stock)
            ->setPrice($data['products_price'])
            ->setIsVariant(false)
            ->setTitle($data['products_description']['products_name'])
            ->setEan($data['products_item_codes']['code_mpn'])
            ->setShortDescription($data['products_description']['products_short_description'])
            ->setLongDescription($data['products_description']['products_description'])
            ->setSearchKeys($data['products_description']['products_keywords'] ?? '');

        return $transfer;
    }
}
