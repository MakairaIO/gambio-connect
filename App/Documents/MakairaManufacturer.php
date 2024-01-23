<?php

namespace GXModules\Makaira\GambioConnect\App\Documents;

use GXModules\Makaira\GambioConnect\App\Documents\MakairaDocument;

class MakairaManufacturer extends MakairaDocument
{
    protected array $mappingFields = [
        'manufacturers_id' => 'id',
        'manufacturers_name',
        'manufacturers_image',
        'date_added',
        'last_modified',
        'language_id',
        'manufacturers_meta_title',
        'manufacturers_meta_description',
        'manufacturers_meta_keywords',
        'manufacturers_url',
        'url_clicked',
        'date_last_click'
    ];
    
    private int $manufacturers_id;
    
    private string $manufacturers_name;
    
    private string $manufacturers_image;
    
    private string $date_added;
    
    private string $last_modified;
    
    private string $language_id = '';
    
    private string $manufacturers_meta_title = '';
    
    private string $manufacturers_meta_description = '';
    
    private string $manufacturers_meta_keywords = '';
    
    private string $manufacturers_url;
    
    private int $url_clicked;
    
    private string $date_last_click;
    
    
    public function getManufacturersId(): int
    {
        return $this->manufacturers_id;
    }
    
    
    public function setManufacturersId(int $manufacturers_id): void
    {
        $this->manufacturers_id = $manufacturers_id;
    }
    
    
    public function getManufacturersName(): string
    {
        return $this->manufacturers_name;
    }
    
    
    public function setManufacturersName(string $manufacturers_name): void
    {
        $this->manufacturers_name = $manufacturers_name;
    }
    
    
    public function getManufacturersImage(): string
    {
        return $this->manufacturers_image;
    }
    
    
    public function setManufacturersImage(string $manufacturers_image): void
    {
        $this->manufacturers_image = $manufacturers_image;
    }
    
    
    public function getDateAdded(): string
    {
        return $this->date_added;
    }
    
    
    public function setDateAdded(string $date_added): void
    {
        $this->date_added = $date_added;
    }
    
    
    public function getLastModified(): string
    {
        return $this->last_modified;
    }
    
    
    public function setLastModified(string $last_modified): void
    {
        $this->last_modified = $last_modified;
    }
    
    
    public function getLanguageId(): string
    {
        return $this->language_id;
    }
    
    
    public function setLanguageId(string $language_id): void
    {
        $this->language_id = $language_id;
    }
    
    
    public function getManufacturersMetaTitle(): string
    {
        return $this->manufacturers_meta_title;
    }
    
    
    public function setManufacturersMetaTitle(string $manufacturers_meta_title): void
    {
        $this->manufacturers_meta_title = $manufacturers_meta_title;
    }
    
    
    public function getManufacturersMetaDescription(): string
    {
        return $this->manufacturers_meta_description;
    }
    
    
    public function setManufacturersMetaDescription(string $manufacturers_meta_description): void
    {
        $this->manufacturers_meta_description = $manufacturers_meta_description;
    }
    
    
    public function getManufacturersMetaKeywords(): string
    {
        return $this->manufacturers_meta_keywords;
    }
    
    
    public function setManufacturersMetaKeywords(string $manufacturers_meta_keywords): void
    {
        $this->manufacturers_meta_keywords = $manufacturers_meta_keywords;
    }
    
    
    public function getManufacturersUrl(): string
    {
        return $this->manufacturers_url;
    }
    
    
    public function setManufacturersUrl(string $manufacturers_url): void
    {
        $this->manufacturers_url = $manufacturers_url;
    }
    
    
    public function getUrlClicked(): int
    {
        return $this->url_clicked;
    }
    
    
    public function setUrlClicked(int $url_clicked): void
    {
        $this->url_clicked = $url_clicked;
    }
    
    
    public function getDateLastClick(): string
    {
        return $this->date_last_click;
    }
    
    
    public function setDateLastClick(string|null $date_last_click): void
    {
        $this->date_last_click = $date_last_click ?? '';
    }
    
    public static function mapFromManufacturer(array $manufacturer): static
    {
        $instance = new self(
            docType: 'manufacturer',
            languageId: '',
            delete: false
        );
        foreach($manufacturer as $field => $value) {
            $setter = self::convertSnakeToCamel('set_' . $field);
            if(method_exists($instance, $setter)) {
                $instance->$setter($value);
            }
        }
        return $instance;
    }
}