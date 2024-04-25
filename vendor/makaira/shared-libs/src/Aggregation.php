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

class Aggregation extends DataObject
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $values;

    /**
     * @var mixed
     */
    public $min;

    /**
     * @var mixed
     */
    public $max;

    /**
     * @var array
     */
    public $selectedValues;

    /**
     * @var integer
     */
    public $position;

    /**
     * @var bool
     */
    public $showDocCount;
}
