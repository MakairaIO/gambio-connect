<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira;

use Kore\DataObject\DataObject;

class Result extends DataObject
{
    /**
     * @var ResultItem[]
     */
    public $items;

    /**
     * @var integer
     */
    public $count;

    /**
     * @var integer
     */
    public $total;

    /**
     * @var integer
     */
    public $offset;

    /**
     * @var Aggregation
     */
    public $aggregations;

    /**
     * @var array
     */
    public $additionalData = [];
}
