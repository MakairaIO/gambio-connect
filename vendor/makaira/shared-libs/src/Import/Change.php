<?php

namespace Makaira\Import;

use Kore\DataObject\DataObject;

class Change extends DataObject
{
    public $id;
    public $path;
    public $sequence;
    public $deleted;
    public $type;
    public $data;
}
