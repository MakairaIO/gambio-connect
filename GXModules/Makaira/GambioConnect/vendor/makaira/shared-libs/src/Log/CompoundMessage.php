<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Log;

class CompoundMessage
{
    public $text = [];

    public function __construct($text)
    {
        if (!empty($text)) {
            $this->text[] = $text;
        }
    }

    public function add($text)
    {
        $this->text[] = $text;
    }

    public function __toString()
    {
        return implode(' | ', $this->text);
    }
}
