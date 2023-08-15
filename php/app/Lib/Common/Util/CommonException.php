<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-13
 * Time: 17:04
 */

namespace App\Lib\Common\Util;

use Exception;


class CommonException extends Exception
{
    public function __construct(int $code = 0, string $message = "")
    {
        $code = !empty($code) ? $code : ErrorCodes::UNKNOWN_ERROR;

        $message = !empty($message) ? $message : ErrorCodes::getCodeMessage($code);

        parent::__construct($message, $code);
    }
}
