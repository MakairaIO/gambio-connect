<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App\Documents;

use DateTime;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;

class MakairaProduct extends MakairaEntity
{
    private int $stock = 0;
    private float $price = 0.0;
    
    private bool $isVariant = false;
    
    private array $attributes = [];
    private array $categories = [];
    
    private string $title = '';
    private string $ean = '';
    private string $shortDescription = '';
    private string $longDescription = '';
    private string $soldAmount = '';
    private string $url = '';
    private string $searchKeys = '';
    private string $mainCategory = '';
    private string $mainCategoryUrl = '';
    private string $manufacturerId = '';
    private string $manufacturerTitle = '';
    
    /* Special makaira fields */
    private float $makBoostNormInsert = 0.0;
    private float $makBoostNormSold = 0.0;
    private float $makBoostNormRating = 0.0;
    private float $makBoostNormRevenue = 0.0;
    private float $makBoostNormProfitMargin = 0.0;
    

    public function toArray(): array
    {
       return [
            /* Makaira fields */
            ...parent::toArray(),
            
            /* Integer fields */
            'stock' => $this->stock,
            'price' => $this->price,
            
            /* Boolean fields */
            'is_variant' => $this->isVariant,
            
            /* Array fields */
            'attributes' => $this->attributes,
            'category' => $this->categories,
            
            /* String fields */
            'title' => $this->title,
            'ean' => $this->ean,
            'shortdesc' => $this->shortDescription,
            'longdesc' => $this->longDescription,
            'soldamount' => $this->soldAmount,
            'url' => $this->url,
            'searchkeys' => $this->searchKeys,
            'maincategory' => $this->mainCategory,
            'maincategoryurl' => $this->mainCategoryUrl,
            'manufacturerid' => $this->manufacturerId,
            'manufacturer_title' => $this->manufacturerTitle,
            
            /* Special makaira fields */
            'mak_boost_norm_insert' => $this->makBoostNormInsert,
            'mak_boost_norm_sold' => $this->makBoostNormSold,
            'mak_boost_norm_rating' => $this->makBoostNormRating,
            'mak_boost_norm_revenue' => $this->makBoostNormRevenue,
            'mak_boost_norm_profit_margin' => $this->makBoostNormProfitMargin,
       ];
    }
    
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    
    public function setTitle(string $title): MakairaProduct
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function getStock(): int
    {
        return $this->stock;
    }
    
    
    public function setStock(int $stock): MakairaProduct
    {
        $this->stock = $stock;
        return $this;
    }
    
    
    public function getPrice(): float
    {
        return $this->price;
    }
    
    
    public function setPrice(float $price): MakairaProduct
    {
        $this->price = $price;
        return $this;
    }
    
    
    public function isVariant(): bool
    {
        return $this->isVariant;
    }
    
    
    public function setIsVariant(bool $isVariant): MakairaProduct
    {
        $this->isVariant = $isVariant;
        return $this;
    }
    
    
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    
    
    public function setAttributes(array $attributes): MakairaProduct
    {
        $this->attributes = $attributes;
        return $this;
    }
    
    
    public function getSearchKeys(): string
    {
        return $this->searchKeys;
    }
    
    
    public function setSearchKeys(string $searchKeys): MakairaProduct
    {
        $this->searchKeys = $searchKeys;
        return $this;
    }
    
    
    public function getCategories(): array
    {
        return $this->categories;
    }
    
    
    public function setCategories(array $categories): MakairaProduct
    {
        $this->categories = $categories;
        return $this;
    }
    
    
    public function getEan(): string
    {
        return $this->ean;
    }
    
    
    public function setEan(string $ean): MakairaProduct
    {
        $this->ean = $ean;
        return $this;
    }
    
    
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }
    
    
    public function setShortDescription(string $shortDescription): MakairaProduct
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }
    
    
    public function getLongDescription(): string
    {
        return $this->longDescription;
    }
    
    
    public function setLongDescription(string $longDescription): MakairaProduct
    {
        $this->longDescription = $longDescription;
        return $this;
    }
    
    
    public function getSoldAmount(): string
    {
        return $this->soldAmount;
    }
    
    
    public function setSoldAmount(string $soldAmount): MakairaProduct
    {
        $this->soldAmount = $soldAmount;
        return $this;
    }
    
    
    public function getUrl(): string
    {
        return $this->url;
    }
    
    
    public function setUrl(string $url): MakairaProduct
    {
        $this->url = $url;
        return $this;
    }
    
    
    public function getMainCategory(): string
    {
        return $this->mainCategory;
    }
    
    
    public function setMainCategory(string $mainCategory): MakairaProduct
    {
        $this->mainCategory = $mainCategory;
        return $this;
    }
    
    
    public function getMainCategoryUrl(): string
    {
        return $this->mainCategoryUrl;
    }
    
    
    public function setMainCategoryUrl(string $mainCategoryUrl): MakairaProduct
    {
        $this->mainCategoryUrl = $mainCategoryUrl;
        return $this;
    }
    
    
    public function getManufacturerId(): string
    {
        return $this->manufacturerId;
    }
    
    
    public function setManufacturerId(string $manufacturerId): MakairaProduct
    {
        $this->manufacturerId = $manufacturerId;
        return $this;
    }
    
    
    public function getManufacturerTitle(): string
    {
        return $this->manufacturerTitle;
    }
    
    
    public function setManufacturerTitle(string $manufacturerTitle): MakairaProduct
    {
        $this->manufacturerTitle = $manufacturerTitle;
        return $this;
    }
    
    
    public function getMakBoostNormInsert(): float
    {
        return $this->makBoostNormInsert;
    }
    
    
    public function setMakBoostNormInsert(float $makBoostNormInsert): MakairaProduct
    {
        $this->makBoostNormInsert = $makBoostNormInsert;
        return $this;
    }
    
    
    public function getMakBoostNormSold(): float
    {
        return $this->makBoostNormSold;
    }
    
    
    public function setMakBoostNormSold(float $makBoostNormSold): MakairaProduct
    {
        $this->makBoostNormSold = $makBoostNormSold;
        return $this;
    }
    
    
    public function getMakBoostNormRating(): float
    {
        return $this->makBoostNormRating;
    }
    
    
    public function setMakBoostNormRating(float $makBoostNormRating): MakairaProduct
    {
        $this->makBoostNormRating = $makBoostNormRating;
        return $this;
    }
    
    
    public function getMakBoostNormRevenue(): float
    {
        return $this->makBoostNormRevenue;
    }
    
    
    public function setMakBoostNormRevenue(float $makBoostNormRevenue): MakairaProduct
    {
        $this->makBoostNormRevenue = $makBoostNormRevenue;
        return $this;
    }
    
    
    public function getMakBoostNormProfitMargin(): float
    {
        return $this->makBoostNormProfitMargin;
    }
    
    
    public function setMakBoostNormProfitMargin(float $makBoostNormProfitMargin): MakairaProduct
    {
        $this->makBoostNormProfitMargin = $makBoostNormProfitMargin;
        return $this;
    }

}
