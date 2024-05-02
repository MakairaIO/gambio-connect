<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use GXModules\Makaira\GambioConnect\App\Documents\MakairaCategory;
use GXModules\Makaira\GambioConnect\App\Documents\MakairaProduct;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;

class GambioConnectPublicFieldsService extends GambioConnectService
{
    public function setUpProductPublicFields(): void
    {
        $publicFields = json_decode($this->client->getPublicFields()->getBody()->getContents());

        $unsetPublicFields = $this->mapUnsetPublicFields(MakairaProduct::FIELDS, $publicFields);

        $this->setPublicFields($unsetPublicFields);
    }

    public function setUpCategoryPublicFields(): void
    {
        $publicFields = json_decode($this->client->getPublicFields()->getBody()->getContents());

        $unsetPublicFields = $this->mapUnsetPublicFields(MakairaCategory::FIELDS, $publicFields);

        $this->setPublicFields($unsetPublicFields);
    }

    private function mapUnsetPublicFields(array $fields, array $publicFields): array
    {
        return array_filter($fields, function (string $field) use ($publicFields) {
            foreach ($publicFields as $publicField) {
                if ($publicField->fieldId === $field || $publicField->fieldName === $field) {
                    return null;
                }
            }
            return $field;
        });
    }

    private function setPublicFields(array $unsetPublicFields): void
    {
        foreach ($unsetPublicFields as $unsetPublicField) {
            $this->client->setPublicField($unsetPublicField);
        }
    }
}
