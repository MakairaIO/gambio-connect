<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App\Documents;

use DateTime;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;

class MakairaProduct extends MakairaEntity
{
    public const FIELD_TYPE = 'type';

    public const FIELD_ID = 'id';

    public const FIELD_PARENT = 'parent';

    public const FIELD_SHOP = 'shop';

    public const FIELD_EAN = 'ean';

    public const FIELD_ACTIVETO = 'activeto';

    public const FIELD_ACTIVEFROM = 'activefrom';

    public const FIELD_IS_VARIANT = 'is_Variant';

    public const FIELD_ACTIVE = 'active';

    public const FIELD_SORT = 'sort';

    public const FIELD_STOCK = 'stock';

    public const FIELD_ONSTOCK = 'onstock';

    public const FIELD_PICTURE_URL_MAIN = 'picture_url_main';

    public const FIELD_TITLE = 'title';

    public const FIELD_SHORTDESC = 'shortdesc';

    public const FIELD_LONGDESC = 'longdesc';

    public const FIELD_PRICE = 'price';

    public const FIELD_SOLDAMOUNT = 'soldamount';

    public const FIELD_SEARCHABLE = 'searchable';

    public const FIELD_SEARCHKEYS = 'searchkeys';

    public const FIELD_URL = 'url';

    public const FIELD_MAINCATEGORY = 'maincategory';

    public const FIELD_MAINCATEGORYURL = 'maincategoryurl';

    public const FIELD_CATEGORY = 'category';

    public const FIELD_ATTRIBUTES = 'attributes';

    public const FIELD_MANUFACTUERID = 'manufacturerid';

    public const FIELD_MANUFACTURER_TITLE = 'manufacturer_title';

    public const FIELD_FSK18 = 'fsk_18';

    public const FIELDS = [
        self::FIELD_ID,
        self::FIELD_TYPE,
        self::FIELD_PARENT,
        self::FIELD_SHOP,
        self::FIELD_EAN,
        self::FIELD_ACTIVETO,
        self::FIELD_ACTIVEFROM,
        self::FIELD_IS_VARIANT,
        self::FIELD_ACTIVE,
        self::FIELD_SORT,
        self::FIELD_STOCK,
        self::FIELD_ONSTOCK,
        self::FIELD_PICTURE_URL_MAIN,
        self::FIELD_TITLE,
        self::FIELD_SHORTDESC,
        self::FIELD_LONGDESC,
        self::FIELD_PRICE,
        self::FIELD_SOLDAMOUNT,
        self::FIELD_SEARCHABLE,
        self::FIELD_SEARCHKEYS,
        self::FIELD_CATEGORY,
        self::FIELD_ATTRIBUTES,
        self::FIELD_MANUFACTUERID,
        self::FIELD_MANUFACTURER_TITLE,
        self::FIELD_URL,
        self::FIELD_MAINCATEGORY,
        self::FIELD_MAINCATEGORYURL,
        self::FIELD_FSK18
    ];

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

    private int $sortOrder = 0;

    private bool $fsk18 = false;

    private int $taxClassId = 0;

    private string $gmAltText = '';

    private int $productsVpe = 0;

    private int $productsVpeStatus = 0;

    private float $productsVpeValue = 0;

    /* Special makaira fields */
    private float $makBoostNormInsert = 0.0;
    private float $makBoostNormSold = 0.0;
    private float $makBoostNormRating = 0.0;
    private float $makBoostNormRevenue = 0.0;
    private float $makBoostNormProfitMargin = 0.0;


    public function toArray(): array
    {
        return array_merge(
            /* Makaira fields */
            parent::toArray(),
            [

             /* Integer fields */
             'stock' => $this->stock,
             'price' => $this->price,
             'tax_class_id' => $this->taxClassId,
             'products_vpe' => $this->productsVpe,
             'products_vpe_status' => $this->productsVpeStatus,
             'products_vpe_value' => $this->productsVpeValue,

             /* Boolean fields */
             'is_variant' => $this->isVariant,
             'onstock' => $this->getStock() > 0,
             'fsk_18' => $this->fsk18,

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
             'gm_alt_text' => $this->gmAltText,

             /* Special makaira fields */
             'mak_boost_norm_insert' => $this->makBoostNormInsert,
             'mak_boost_norm_sold' => $this->makBoostNormSold,
             'mak_boost_norm_rating' => $this->makBoostNormRating,
             'mak_boost_norm_revenue' => $this->makBoostNormRevenue,
             'mak_boost_norm_profit_margin' => $this->makBoostNormProfitMargin,
            ]
        );
    }


    private function mapProduct(): array
    {
        if (!$this->product) {
            return false;
        }

        $document = [
            'data' =>  [
                'type'                         => self::DOC_TYPE,
                'id'                           => $this->product['products_id'],
                'parent'                       => '',
                'shop'                         => 1,
                'ean'                          => $this->product['products_ean'],
                'activeto'                     => '',
                'activefrom'                   => '',
                'isVariant'                    => false,
                'active'                       => $this->getActive(),
                'sort'                         => 0,
                'stock'                        => $this->getStock(),
                'onstock'                      => $this->getStock() > 0,
                'picture_url_main'             => $this->product['products_image'],
                'title'                        => $this->product['products_name'],
                'shortdesc'                    => $this->product['products_short_description'],
                'longdesc'                     => $this->product['products_description'],
                'price'                        => $this->product['products_price'],
                'soldamount'                   => "",
                'searchable'                   => true,
                'searchkeys'                   => $this->product['products_keywords'] ?? '',
                'url'                          => $this->getUrl(),
                'maincategory'                 => $this->product['main_category_id'],
                'maincategoryurl'              => "",
                'category'                     => [],
                'attributes'                   => [],
                'mak_boost_norm_insert'        => 0.0,
                'mak_boost_norm_sold'          => 0.0,
                'mak_boost_norm_rating'        => 0.0,
                'mak_boost_norm_revenue'       => 0.0,
                'mak_boost_norm_profit_margin' => 0.0,
                'timestamp'                    => $this->now,
                'manufacturerid'               => $this->product['manufacturers_id'],
                'manufacturer_title'           => '',
            ],
            'source_revision' => 1,
            'language_id' => $this->getLanguage()
        ];
        if ($this->delete) {
            $document['delete'] = true;
        }

        return $document;
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


    public function setFsk18(bool $fsk18): static
    {
        $this->fsk18 = $fsk18;

        return $this;
    }


    public function setTaxClassId(int $taxClassId): static
    {
        $this->taxClassId = $taxClassId;

        return $this;
    }


    public function setGmAltText(string $gmAltText): static
    {
        $this->gmAltText = $gmAltText;

        return $this;
    }


    public function setProductsVpe(int $productsVpe): static
    {
        $this->productsVpe = $productsVpe;

        return $this;
    }


    public function setProductsVpeStatus(int $productsVpeStatus): static
    {
        $this->productsVpeStatus = $productsVpeStatus;

        return $this;
    }


    public function setProductsVpeValue(float $productsVpeValue): static
    {
        $this->productsVpeValue = $productsVpeValue;

        return $this;
    }


    public function isFsk18(): bool
    {
        return $this->fsk18;
    }


    public function getTaxClassId(): int
    {
        return $this->taxClassId;
    }


    public function getGmAltText(): string
    {
        return $this->gmAltText;
    }


    public function getProductsVpe(): int
    {
        return $this->productsVpe;
    }


    public function getProductsVpeStatus(): int
    {
        return $this->productsVpeStatus;
    }


    public function getProductsVpeValue(): int
    {
        return $this->productsVpeValue;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }
}
