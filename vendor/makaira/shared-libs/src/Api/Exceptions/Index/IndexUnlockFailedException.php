<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 25.06.18
 * Time: 12:48
 */

namespace Makaira\Api\Exceptions\Index;

use Makaira\Api\Exceptions\IndexException;

/**
 * exception thrown if an index can not be unlocked
 * error can only be found on runtime occurs
 */
class IndexUnlockFailedException extends IndexException
{

}
