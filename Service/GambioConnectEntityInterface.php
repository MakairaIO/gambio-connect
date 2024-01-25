<?php

namespace GXModules\Makaira\GambioConnect\Service;

interface GambioConnectEntityInterface
{
    public function export(): void;
    
    public function replace(): void;
    
    public function switch(): void;
    
    public function prepareExport(): void;
}