<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-22
 * Time: 22:17
 */

namespace App\Lib\Common\Util;

use Illuminate\Support\Facades\Hash;

class EncryptHelper
{
    public static function encryptPasswordBcrypt($string)
    {
        return Hash::make($string);
    }


    /**
     * Bcrypt算法加密验证密码
     * @param $string string 待验证的密文
     * @param $encryption string 已加密的密文
     * @return bool
     */
    public static function verifyPasswordBcrypt($string, $encryption)
    {
        if (Hash::check($string, $encryption)) {
            return true;
        }
        return false;
    }
}
