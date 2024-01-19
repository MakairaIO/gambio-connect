<?php

namespace GXModules\Makaira\GambioConnect\App\Documents;

use Psr\Log\LoggerInterface;

class MakairaCategory extends MakairaDocument
{
    protected array $mappingFields = [
        'categories_id' => 'id',
        'categories_image',
        'parent_id',
        'categories_status',
        'status',
        'sort_order',
        'products_sorting',
        'products_sorting2',
        'date_added',
        'last_modified',
        'categories_icon',
        'categories_icon_w',
        'categories_icon_h',
        'language_id',
        'categories_name',
        'categories_heading_title',
        'categories_description',
        'categories_description_bottom',
        'categories_meta_title',
        'categories_meta_description',
        'categories_meta_keywords',
        'gm_alt_text',
        'gm_url_keywords',
        'shop_id',
        'hierarchy',
        'depth',
        'subcategories'
    ];
    
    private int $categories_id;
    
    private string $categories_image;
    
    private int $parent_id;
    
    private int $categories_status;
    
    private int $status;
    
    private int $sort_order;
    
    private string $products_sorting;
    
    private string $products_sorting2;
    
    private string $date_added;
    
    private string $last_modified;
    
    private string $categories_icon;
    
    private int $categories_icon_w;
    
    private int $categories_icon_h;
    
    private string $language_id;
    
    private string $categories_name;
    
    private string $categories_heading_title;
    
    private string $categories_description;
    
    private string $categories_description_bottom;
    
    private string $categories_meta_title;
    
    private string $categories_meta_description;
    
    private string $categories_meta_keywords;
    
    private string $gm_alt_text;
    
    private string $gm_url_keywords;
    
    private int $depth;
    
    private string $hierarchy;
    
    private array $subcategories = [];
    
    public function getCategoriesId(): int
    {
        return $this->categories_id;
    }
    
    
    public function setCategoriesId(int $categories_id): void
    {
        $this->categories_id = $categories_id;
    }
    
    
    public function getCategoriesImage(): string
    {
        return $this->categories_image;
    }
    
    
    public function setCategoriesImage(string $categories_image): void
    {
        $this->categories_image = $categories_image;
    }
    
    
    public function getParentId(): int
    {
        return $this->parent_id;
    }
    
    
    public function setParentId(int $parent_id): void
    {
        $this->parent_id = $parent_id;
    }
    
    
    public function getCategoriesStatus(): int
    {
        return $this->categories_status;
    }
    
    
    public function setCategoriesStatus(int $categories_status): void
    {
        $this->categories_status = $categories_status;
    }
    
    
    public function getStatus(): int
    {
        return $this->status;
    }
    
    
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }
    
    
    public function getSortOrder(): int
    {
        return $this->sort_order;
    }
    
    
    public function setSortOrder(int $sort_order): void
    {
        $this->sort_order = $sort_order;
    }
    
    
    public function getProductsSorting(): string
    {
        return $this->products_sorting;
    }
    
    
    public function setProductsSorting(string $products_sorting): void
    {
        $this->products_sorting = $products_sorting;
    }
    
    
    public function getProductsSorting2(): string
    {
        return $this->products_sorting2;
    }
    
    
    public function setProductsSorting2(string $products_sorting2): void
    {
        $this->products_sorting2 = $products_sorting2;
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
    
    
    public function getCategoriesIcon(): string
    {
        return $this->categories_icon;
    }
    
    
    public function setCategoriesIcon(string $categories_icon): void
    {
        $this->categories_icon = $categories_icon;
    }
    
    
    public function getCategoriesIconW(): int
    {
        return $this->categories_icon_w;
    }
    
    
    public function setCategoriesIconW(int $categories_icon_w): void
    {
        $this->categories_icon_w = $categories_icon_w;
    }
    
    
    public function getCategoriesIconH(): int
    {
        return $this->categories_icon_h;
    }
    
    
    public function setCategoriesIconH(int $categories_icon_h): void
    {
        $this->categories_icon_h = $categories_icon_h;
    }
    
    
    public function getLanguageId(): string
    {
        return $this->language_id;
    }
    
    
    public function setLanguageId(string $languageId): void
    {
        $this->language_id = $languageId;
    }
    
    
    public function getCategoriesName(): string
    {
        return $this->categories_name;
    }
    
    
    public function setCategoriesName(string $categories_name): void
    {
        $this->categories_name = $categories_name;
    }
    
    
    public function getCategoriesHeadingTitle(): string
    {
        return $this->categories_heading_title;
    }
    
    
    public function setCategoriesHeadingTitle(string $categories_heading_title): void
    {
        $this->categories_heading_title = $categories_heading_title;
    }
    
    
    public function getCategoriesDescription(): string
    {
        return $this->categories_description;
    }
    
    
    public function setCategoriesDescription(string $categories_description): void
    {
        $this->categories_description = $categories_description;
    }
    
    
    public function getCategoriesDescriptionBottom(): string
    {
        return $this->categories_description_bottom;
    }
    
    
    public function setCategoriesDescriptionBottom(string $categories_description_bottom): void
    {
        $this->categories_description_bottom = $categories_description_bottom;
    }
    
    
    public function getCategoriesMetaTitle(): string
    {
        return $this->categories_meta_title;
    }
    
    
    public function setCategoriesMetaTitle(string $categories_meta_title): void
    {
        $this->categories_meta_title = $categories_meta_title;
    }
    
    
    public function getCategoriesMetaDescription(): string
    {
        return $this->categories_meta_description;
    }
    
    
    public function setCategoriesMetaDescription(string $categories_meta_description): void
    {
        $this->categories_meta_description = $categories_meta_description;
    }
    
    
    public function getCategoriesMetaKeywords(): string
    {
        return $this->categories_meta_keywords;
    }
    
    
    public function setCategoriesMetaKeywords(string $categories_meta_keywords): void
    {
        $this->categories_meta_keywords = $categories_meta_keywords;
    }
    
    
    public function getGmAltText(): string
    {
        return $this->gm_alt_text;
    }
    
    
    public function setGmAltText(string $gm_alt_text): void
    {
        $this->gm_alt_text = $gm_alt_text;
    }
    
    
    public function getGmUrlKeywords(): string
    {
        return $this->gm_url_keywords;
    }
    
    
    public function setGmUrlKeywords(string $gm_url_keywords): void
    {
        $this->gm_url_keywords = $gm_url_keywords;
    }
    
    
    public function getDepth(): int
    {
        return $this->depth;
    }
    
    
    public function setDepth(int $depth): void
    {
        $this->depth = $depth;
    }
    
    
    public function getHierarchy(): string
    {
        return $this->hierarchy;
    }
    
    
    public function setHierarchy(string $hierarchy): void
    {
        $this->hierarchy = $hierarchy;
    }
    
    
    public function getSubcategories(): array
    {
        return $this->subcategories;
    }
    
    
    public function setSubcategories(array $subcategories): void
    {
        $this->subcategories = $subcategories;
    }
    
    
    public static function mapFromCategory(array $category): static
    {
        $instance = new MakairaCategory(
            docType: 'category',
            languageId: $category['language_id'],
            delete: false
        );
        foreach($category as $field => $value) {
            $setter = self::convertSnakeToCamel('set_' . $field);
            if(method_exists($instance, $setter)) {
                $instance->$setter($value);
            }
        }
        return $instance;
    }
    
    public function toArray(): array
    {
        $data = parent::toArray();
        
        foreach($this->mappingFields as $key => $field) {
            if(is_int($key)) {
                $getter = self::convertSnakeToCamel('get_' . $field);
                $data[self::convertSnakeToCamel($field)] = $this->$getter();
            } else {
                $getter = self::convertSnakeToCamel('get_' . $key);
                $data[self::convertSnakeToCamel($field)] = $this->$getter();
            }
        }
        return $data;
    }
    
    public static function convertSnakeToCamel(string $snakeString): string
    {
        $parts = explode('_', $snakeString);
        $camel = array_shift($parts);
        $camel = str_replace('_', '', $camel);
        foreach($parts as $index => $part) {
            $camel .= ucfirst($part);
        }
        return $camel;
    }
}