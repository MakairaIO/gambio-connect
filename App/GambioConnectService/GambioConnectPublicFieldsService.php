<?php

namespace GXModules\Makaira\GambioConnect\App\GambioConnectService;

use GXModules\Makaira\GambioConnect\App\Documents\MakairaProduct;
use GXModules\Makaira\GambioConnect\App\GambioConnectService;

class GambioConnectPublicFieldsService extends GambioConnectService
{
    public function setUpPublicFields(): void
    {
        $publicFields = json_decode($this->client->getPublicFields()->getBody()->getContents());
        $unsetPublicFields = array_filter(MakairaProduct::FIELDS, function(string $field) use($publicFields) {
            foreach($publicFields as $publicField) {
                if($publicField->fieldId === $field || $publicField->fieldName === $field) {
                    return null;
                }
            }
            return $field;
        });

        foreach($unsetPublicFields as $unsetPublicField) {
            $this->client->setPublicField($unsetPublicField);
        }
    }
}