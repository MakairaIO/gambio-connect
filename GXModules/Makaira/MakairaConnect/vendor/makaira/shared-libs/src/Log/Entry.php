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

use Kore\DataObject\DataObject;

class Entry extends DataObject
{
    public $level;
    public $message;
    public $messageList;

    public function __construct(array $values = array())
    {
        $message = '';
        if (isset($values['message'])) {
            $message = $values['message'];
            unset($values['message']);
        }

        $this->messageList = new CompoundMessage($message);
        parent::__construct($values);
    }
}
