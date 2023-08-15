<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-15
 * Time: 19:36
 */

namespace App\Lib\Common\Util;

use Exception;

class ParamException extends Exception
{
    public function __construct(string $message = "", int $code = 0)
    {

        parent::__construct($message, $code);
    }
}
