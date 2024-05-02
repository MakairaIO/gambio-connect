<?php

namespace GXModules\Makaira\MakairaConnect\App\Documents;

use GXModules\Makaira\MakairaConnect\Admin\Actions\App\Documents\MakairaEntity;use Psr\Log\LoggerInterface;

class MakairaCategory extends MakairaEntity
{
    public const FIELD_PARENT = 'parent';

    public const FIELD_CATEGORIES_DESCRIPTION = 'categories_description';

    public const FIELD_CATEGORIES_DESCRIPTION_BOTTOM = 'categories_description_bottom';

    public const FIELD_CATEGORIES_NAME = 'categories_name';

    public const FIELD_CATEGORIES_HEADING_TITLE = 'categories_heading_title';

    public const FIELD_GM_ALT_TEXT = 'gm_alt_text';

    public const FIELD_SHOW_SUB_CATEOGORIES = 'show_sub_categories';

    public const FIELD_SHOW_SUB_CATEGORIES_IMAGES = 'show_sub_categories_images';

    public const FIELD_SHOW_SUB_CATEGORIES_NAMES = 'show_sub_categories_names';

    public const FIELD_SHOW_CATEGORIES_IMAGE_IN_DESCRIPTION = 'show_categories_image_in_description';

    public const FIELD_SHOW_SUB_PRODUCTS = 'show_sub_products';

    public const FIELD_CATEGORIES_TEMPLATE = 'categories_template';

    public const FIELD_VIEW_MODE_TILES = 'view_mode_tiled';

    public const FIELD_CATEGORIES_IMAGE = 'categories_image';

    public const FIELD_GM_SHOW_QTY_INFO = 'gm_show_qty_info';

    public const FIELDS = [
        self::FIELD_PARENT,
        self::FIELD_CATEGORIES_DESCRIPTION,
        self::FIELD_CATEGORIES_DESCRIPTION_BOTTOM,
        self::FIELD_CATEGORIES_NAME,
        self::FIELD_CATEGORIES_HEADING_TITLE,
        self::FIELD_GM_ALT_TEXT,
        self::FIELD_SHOW_SUB_CATEOGORIES,
        self::FIELD_SHOW_SUB_CATEGORIES_IMAGES,
        self::FIELD_SHOW_SUB_CATEGORIES_NAMES,
        self::FIELD_SHOW_CATEGORIES_IMAGE_IN_DESCRIPTION,
        self::FIELD_SHOW_SUB_PRODUCTS,
        self::FIELD_CATEGORIES_TEMPLATE,
        self::FIELD_VIEW_MODE_TILES,
        self::FIELD_CATEGORIES_IMAGE,
        self::FIELD_GM_SHOW_QTY_INFO
    ];

    private int $depth = 0;
    private int $sort = 0;

    private string $categoryTitle = '';

    private string $categoryDescription = '';

    private string $categoryDescriptionBottom = '';

    private string $categoryHeadingTitle = '';

    private string $gm_alt_text = '';

    private bool $showSubCategories = false;

    private bool $showSubCategoriesImages = false;

    private bool $showSubCategoriesNames = false;

    private bool $showCategoriesImageInDescription = false;

    private bool $showSubProducts = false;

    private string $categoriesTemplate = '';

    private string $categoriesId;

    private bool $viewModeTiled = false;

    private string $categoriesImage  = '';

    private string $hierarchy = '';
    private ?string $url = null;

    private array $subCategories = [];
    private array $selfLinks = [];

    private bool $gmShowQtyInfo = false;


    public function toArray(): array
    {
        return array_merge(
            /* Makaira fields */
            parent::toArray(),
            [
                /* Category fields */
                'categories_description' => $this->getCategoryDescription(),
                'categories_description_bottom' => $this->getCategoryDescriptionBottom(),
                'categories_name' => $this->getCategoryTitle(),
                'categories_heading_title' => $this->getCategoryHeadingTitle(),
                'gm_alt_text' => $this->getGmAltText(),
                'show_sub_categories' => $this->isShowSubCategories(),
                'show_sub_categories_images' => $this->isShowSubCategoriesImages(),
                'show_sub_categories_names' => $this->isShowSubCategoriesNames(),
                'show_categories_image_in_description' => $this->isShowCategoriesImageInDescription(),
                'show_sub_products' => $this->isShowSubProducts(),
                'categories_template' => $this->getCategoriesTemplate(),
                'categories_id' => $this->getId(),
                'view_mode_tiled' => $this->isViewModeTiled(),
                'categories_image' => $this->getCategoriesImage(),
                'depth' => $this->getDepth(),
                'sort' => $this->getSort(),
                'category_title' => $this->getCategoryTitle(),
                'hierarchy' => $this->getHierarchy(),
                'url' => $this->getUrl(),
                'subcategories' => $this->getSubCategories(),
                'selfLinks' => $this->getSelfLinks(),
                'gm_show_qty_info' => $this->isGmShowQtyInfo()
            ]
        );
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

    public function getCategoryDescription(): string
    {
        return $this->categoryDescription;
    }

    public function setCategoryDescription(string $categoryDescription): static
    {
        $this->categoryDescription = $categoryDescription;

        return $this;
    }

    public function getCategoryDescriptionBottom(): string
    {
        return $this->categoryDescriptionBottom;
    }

    public function setCategoryDescriptionBottom(string $categoryDescriptionBottom): static
    {
        $this->categoryDescriptionBottom = $categoryDescriptionBottom;

        return $this;
    }

    public function getCategoryHeadingTitle(): string
    {
        return $this->categoryHeadingTitle;
    }

    public function setCategoryHeadingTitle(string $categoryHeadingTitle): static
    {
        $this->categoryHeadingTitle = $categoryHeadingTitle;

        return $this;
    }

    public function getGmAltText(): string
    {
        return $this->gm_alt_text;
    }

    public function setGmAltText(string $gm_alt_text): static
    {
        $this->gm_alt_text = $gm_alt_text;

        return $this;
    }

    public function isShowSubCategories(): bool
    {
        return $this->showSubCategories;
    }

    public function setShowSubCategories(bool $showSubCategories): static
    {
        $this->showSubCategories = $showSubCategories;

        return $this;
    }

    public function isShowSubCategoriesImages(): bool
    {
        return $this->showSubCategoriesImages;
    }

    public function setShowSubCategoriesImages(bool $showSubCategoriesImages): static
    {
        $this->showSubCategoriesImages = $showSubCategoriesImages;

        return $this;
    }

    public function isShowSubCategoriesNames(): bool
    {
        return $this->showSubCategoriesNames;
    }

    public function setShowSubCategoriesNames(bool $showSubCategoriesNames): static
    {
        $this->showSubCategoriesNames = $showSubCategoriesNames;

        return $this;
    }

    public function isShowSubProducts(): bool
    {
        return $this->showSubProducts;
    }

    public function setShowSubProducts(bool $showSubProducts): static
    {
        $this->showSubProducts = $showSubProducts;

        return $this;
    }

    public function getCategoriesTemplate(): string
    {
        return $this->categoriesTemplate;
    }

    public function setCategoriesTemplate(string $categoriesTemplate): static
    {
        $this->categoriesTemplate = $categoriesTemplate;

        return $this;
    }

    public function getCategoriesId(): string
    {
        return $this->categoriesId;
    }

    public function setCategoriesId(string $categoriesId): static
    {
        $this->categoriesId = $categoriesId;

        return $this;
    }

    public function isViewModeTiled(): bool
    {
        return $this->viewModeTiled;
    }

    public function setViewModeTiled(bool $viewModeTiled): static
    {
        $this->viewModeTiled = $viewModeTiled;

        return $this;
    }

    public function getCategoriesImage(): string
    {
        return $this->categoriesImage;
    }

    public function setCategoriesImage(string $categoriesImage): static
    {
        $this->categoriesImage = $categoriesImage;

        return $this;
    }

    public function isShowCategoriesImageInDescription(): bool
    {
        return $this->showCategoriesImageInDescription;
    }

    public function setShowCategoriesImageInDescription(bool $showCategoriesImageInDescription): static
    {
        $this->showCategoriesImageInDescription = $showCategoriesImageInDescription;

        return $this;
    }

    public function isGmShowQtyInfo(): bool
    {
        return $this->gmShowQtyInfo;
    }

    public function setGmShowQtyInfo(bool $gmShowQtyInfo): static
    {
        $this->gmShowQtyInfo = $gmShowQtyInfo;
        return $this;
    }
}
