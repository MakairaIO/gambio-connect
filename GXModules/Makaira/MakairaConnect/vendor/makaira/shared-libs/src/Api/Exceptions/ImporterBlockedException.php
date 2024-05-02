<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 25.06.18
 * Time: 12:48
 */

namespace Makaira\Api\Exceptions;

use Makaira\Api\Exception as ApiException;
use Throwable;

/**
 * exception thrown if an index is locked
 * error can only be found on runtime occurs
 */
class ImporterBlockedException extends ApiException
{
    public function __construct(
        $message = 'Importer blocked',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
