<?php

namespace GXModules\Makaira\GambioConnect\App\Documents;

use Psr\Log\LoggerInterface;

class MakairaCategory extends MakairaEntity
{
    private int $depth = 0;
    private int $sort = 0;

    private string $categoryTitle;
    private string $hierarchy = '';
    private ?string $url = null;

    private array $subCategories = [];
    private array $selfLinks = [];


    public function toArray(): array
    {
        return [
            /* Makaira fields */
            ...parent::toArray(),

            /* Category fields */
            'depth' => $this->depth,
            'sort' => $this->sort,
            'category_title' => $this->categoryTitle,
            'hierarchy' => $this->hierarchy,
            'url' => $this->url,
            'subcategories' => $this->subCategories,
            'selfLinks' => $this->selfLinks,
        ];
    }


    public function getDepth(): int
    {
        return $this->depth;
    }


    public function setDepth(int $depth): MakairaCategory
    {
        $this->depth = $depth;

        return $this;
    }


    public function getSort(): int
    {
        return $this->sort;
    }


    public function setSort(int $sort): MakairaCategory
    {
        $this->sort = $sort;

        return $this;
    }

    public function getCategoryTitle(): string
    {
        return $this->categoryTitle;
    }


    public function setCategoryTitle(string $categoryTitle): MakairaCategory
    {
        $this->categoryTitle = $categoryTitle;

        return $this;
    }


    public function getHierarchy(): string
    {
        return $this->hierarchy;
    }


    public function setHierarchy(string $hierarchy): MakairaCategory
    {
        $this->hierarchy = $hierarchy;

        return $this;
    }


    public function getUrl(): ?string
    {
        return $this->url;
    }


    public function setUrl(?string $url): MakairaCategory
    {
        $this->url = $url;

        return $this;
    }


    public function getSubCategories(): array
    {
        return $this->subCategories;
    }


    public function setSubCategories(array $subCategories): MakairaCategory
    {
        $this->subCategories = $subCategories;

        return $this;
    }


    public function getSelfLinks(): array
    {
        return $this->selfLinks;
    }


    public function setSelfLinks(array $selfLinks): MakairaCategory
    {
        $this->selfLinks = $selfLinks;

        return $this;
    }
}
