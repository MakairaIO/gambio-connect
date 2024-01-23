<?php

namespace GXModules\Makaira\GambioConnect\App\Documents;

abstract class MakairaDocument
{
    protected array $mappingFields = [];
    
    protected int $shopId = 1;
    
    public function __construct(
        private string $docType,
        private string $languageId,
        private bool $delete,
    ) {
        return $this;
    }
    
    public function getDocType(): string
    {
        return $this->docType;
    }
    
    public function getLanguageId(): string
    {
        return $this->languageId;
    }
    
    public function getDelete(): bool
    {
        return $this->delete;
    }
    
    public function getShopId(): int
    {
        return $this->shopId;
    }
    
    
    public function setDocType(string $docType): void
    {
        $this->docType = $docType;
    }
    
    
    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }
    
    
    public function setDelete(bool $delete): void
    {
        $this->delete = $delete;
    }
    
    public function toArray(): array
    {
        $data = [
            'type' => $this->getDocType()
        ];
        
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