<?php

use GXModules\Makaira\GambioConnect\Admin\Services\ModuleConfigService;
use GXModules\Makaira\GambioConnect\App\Core\MakairaRequest;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaCategory;
use GXModules\Makaira\GambioConnect\App\MakairaClient;

class MakairaCategoryListingThemeContentView extends CategoryListingThemeContentView
{
    private ModuleConfigService $configurationStorage;

    private MakairaClient $makairaClient;

    private array $subcategories = [];

    public function __construct($p_template = 'default')
    {
        parent::__construct($p_template);

        $configurationService = LegacyDependencyContainer::getInstance()->get(\Gambio\Core\Configuration\Services\ConfigurationService::class);

        $this->makairaClient = new MakairaClient($configurationService);
    }

    protected function _buildCategoryArray()
    {
        $result = $this->makairaClient->getCategory($this->currentCategoryId);

        $resultCategory = $result->category->items[0] ?? new stdClass();
        $this->categoryArray = [
            'categories_id' => $resultCategory->id,
        ];

        $this->subcategories = $resultCategory->fields->subcategories ?? [];
        $this->mapMakairaFieldsToGambio($resultCategory);
    }

    private function mapMakairaFieldsToGambio(object $resultCategory, string $targetArray = 'categoryArray')
    {
        foreach (MakairaCategory::FIELDS as $field) {
            $this->$targetArray[$field] = $resultCategory->fields->$field;
        }
    }

    protected function _buildSubcategoriesArray()
    {
        $image = '';

        foreach ($this->subcategories as $subcategory) {
            $result = $this->makairaClient->getCategory($subcategory);
            $resultCategory = $result->category->items[0] ?? [];
            $categoryArray = array_merge(
                [
                    'categories_id' => $resultCategory->id,
                ],
                (array)$resultCategory->fields
            );
            $this->subcategoriesArray[] = [
                'CATEGORIES_ID' => $resultCategory->id,
                'CATEGORIES_NAME' => $resultCategory->fields->categories_name,
                'CATEGORIES_ALT_TEXT' => $resultCategory->fields->gm_alt_text,
                'CATEGORIES_HEADING_TITLE' => $resultCategory->fields->categories_heading_title,
                'CATEGORIES_IMAGE' => $image = $this->_buildImageUrl($categoryArray),
                'CATEGORIES_LINK' => $this->_buildCategoryUrl($categoryArray),
                'CATEGORIES_DESCRIPTION' => $resultCategory->fields->categories_description,
                'CATEGORIES_DESCRIPTION_BOTTOM' => $resultCategory->fields->categories_description_bottom
            ];
        }

        return $image;
    }
}
