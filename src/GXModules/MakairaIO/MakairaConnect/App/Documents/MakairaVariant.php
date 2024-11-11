<?php

declare(strict_types=1);

namespace GXModules\MakairaIO\MakairaConnect\App\Documents;

use DateTime;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;

class MakairaVariant extends MakairaEntity
{
    private array $makairaDocument = [];

    private array $product;

    private int $parent;

    private string $ean;

    private bool $isVariant = true;

    private bool $active = true;

    private int $sort;

    private int $stock;

    private string $picture_url_main = '';

    private string $title;

    private string $shortdesc;

    private string $longdesc;

    private float $price;

    private string $searchkeys;

    private string $meta_keywords;

    private string $meta_description;

    private string $maincategory;

    private string $maincategoryurl;

    private ProductVariants $variants;

    private array $options = [];

    private string $now;

    public function __construct()
    {
        $this->now = (new DateTime)->format('Y-m-d H:i:s');
    }

    public function setProduct(array $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            /* Makaira fields */
            parent::toArray(),
            [

                /* Product fields */

                'id' => $this->getId(),
                'type' => $this->getType(),
                'parent' => $this->getParent(),
                'shop' => $this->getShop(),
                'ean' => $this->getEan(),
                'isVariant' => $this->isVariant(),
                'active' => $this->isActive(),
                'stock' => $this->getStock(),
                'onstock' => $this->isOnstock(),
                'title' => $this->getTitle(),
                'shortdesc' => $this->getShortdesc(),
                'longdesc' => $this->getLongdesc(),
                'price' => $this->getPrice(),
                'meta_keywords' => $this->getMetaKeywords(),
                'meta_description' => $this->getMetaDescription(),
            ]
        );
    }

    public function getParent(): int
    {
        return $this->parent;
    }

    public function setParent(int $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getEan(): string
    {
        return $this->ean;
    }

    public function setEan(string $ean): static
    {
        $this->ean = $ean;

        return $this;
    }

    public function isVariant(): bool
    {
        return $this->isVariant;
    }

    public function setIsVariant(bool $isVariant): static
    {
        $this->isVariant = $isVariant;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function isOnstock(): bool
    {
        return $this->onStock;
    }

    public function setOnstock(bool $onStock): static
    {
        $this->onStock = $onStock;

        return $this;
    }

    public function getPictureUrlMain(): string
    {
        return $this->picture_url_main;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getShortdesc(): string
    {
        return $this->shortdesc;
    }

    public function setShortdesc(string $shortdesc): static
    {
        $this->shortdesc = $shortdesc;

        return $this;
    }

    public function getLongdesc(): string
    {
        return $this->longdesc;
    }

    public function setLongdesc(string $longdesc): static
    {
        $this->longdesc = $longdesc;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSearchkeys(): string
    {
        return $this->searchkeys;
    }

    public function setSearchkeys(string $searchkeys): static
    {
        $this->searchkeys = $searchkeys;

        return $this;
    }

    public function getMetaKeywords(): string
    {
        return $this->meta_keywords;
    }

    public function setMetaKeywords(string $meta_keywords): static
    {
        $this->meta_keywords = $meta_keywords;

        return $this;
    }

    public function getMetaDescription(): string
    {
        return $this->meta_description;
    }

    public function setMetaDescription(string $meta_description): static
    {
        $this->meta_description = $meta_description;

        return $this;
    }

    public function getMaincategory(): string
    {
        return $this->maincategory;
    }

    public function setMaincategory(string $maincategory): static
    {
        $this->maincategory = $maincategory;

        return $this;
    }

    public function getMaincategoryurl(): string
    {
        return $this->maincategoryurl;
    }

    public function setMaincategoryurl(string $maincategoryurl): static
    {
        $this->maincategoryurl = $maincategoryurl;

        return $this;
    }

    public function addMakairaDocumentWrapper(): array
    {
        return [
            'items' => $this->makairaDocument,
            'import_timestamp' => $this->now,
            'source_identifier' => 'gambio',
        ];
    }
}
