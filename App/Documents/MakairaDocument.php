<?php

namespace GXModules\Makaira\GambioConnect\App\Documents;

abstract class MakairaDocument
{
    protected array $mappingFields = [];
    
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
}