<?php

namespace Makaira\Import;

use Kore\DataObject\DataObject;

class Changes extends DataObject
{
    public $type;
    public $since;
    public $indexName;
    public $count;
    public $changes;
    public $active;
    public $language;
    public $highLoad = false;
    public $requestedCount;
}
