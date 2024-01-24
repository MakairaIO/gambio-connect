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
    public function mapManufacturer(array $data): MakairaManufacturer
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
    public static function mapCategory(array $data, array $hierarchy) : MakairaCategory
    {
        $transfer = new MakairaCategory();
        
        $transfer
            ->setType(MakairaEntity::DOC_TYPE_CATEGORY)
            ->setId($data['categories_id'])
            ->setCategoryTitle($data['categories_name']);
            //->setHierarchy($hierarchy);
            
        return $transfer;
    }
    
    public static function mapProduct(array $data): MakairaProduct
    {
        $transfer = new MakairaProduct();
    
        $stock = 1;
    
        $transfer->setType(MakairaEntity::DOC_TYPE_PRODUCT)
            ->setId($data['products_id'])
            ->setTitle($data['products_name'])
            ->setStock($stock)
            ->setOnStock($stock > 0)
            ->setPrice($data['products_price'])
            ->setIsVariant(false)
            ->setEan($data['products_ean'])
            ->setShortDescription($data['products_short_description'])
            ->setLongDescription($data['products_description'])
            ->setUrl('')
            ->setSearchKeys($data['products_keywords'] ? : '')
            ->setMainCategory($data['main_category_id'])
            ->setMainCategoryUrl('')
            ->setManufacturerId($data['manufacturers_id'])
            ->setManufacturerTitle('')
            ->setPictureUrlMain($data['products_image'])
    
    }
}
