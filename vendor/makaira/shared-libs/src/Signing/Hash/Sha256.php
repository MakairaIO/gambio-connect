<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * @version    0.1
 * @author     Stefan Krenz <krenz@marmalade.de>
 * @link       http://www.marmalade.de
 */

namespace Makaira\Signing\Hash;

use Makaira\Signing\HashGenerator;

class Sha256 extends HashGenerator
{
    public function hash($nonce, $body, $secret)
    {
        return hash_hmac('sha256', $nonce . ':' . $body, $secret);
    }
}
