<?php

namespace GXModules\Makaira\GambioConnect\App\Mapper;

use DateTime;
use Gambio\Admin\Modules\Language\Model\Language;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaEntity;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaManufacturer;

class MakairaManufacturerMapper
{
    /**
     * @throws \Exception
     */
    public function map(array $data, Language $language): MakairaManufacturer
    {
        $transfer = new MakairaManufacturer();
        
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
            ->setLanguage($language->name())
            ->setMetaTitle($data['manufacturers_meta_title'])
            ->setMetaDescription($data['manufacturers_meta_description'])
            ->setMetaKeywords($data['manufacturers_meta_keywords'])
            ->setRemoteUrl($data['manufacturers_url'])
            ->setIsUrlClicked($data['url_clicked'])
            ->setLastClickedAt($lastClickedAt);
            
        return $transfer;
    }
}
