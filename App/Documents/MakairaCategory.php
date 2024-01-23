<?php

namespace GXModules\Makaira\GambioConnect\App\Documents;

use Psr\Log\LoggerInterface;

class MakairaCategory extends MakairaEntity
{
    
    private string $title;
    private string $hierarchy = '';
    
    
    public function toArray(): array
    {
        return [
            /* Makaira fields */
            ...parent::toArray(),
            
            /* Category fields */
            'title' => $this->title,
            'hierarchy' => $this->hierarchy,
        ];
    }
    
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    
    public function setTitle(string $title): MakairaCategory
    {
        $this->title = $title;
        
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
}
