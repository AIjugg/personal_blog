<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-16
 * Time: 21:17
 */

namespace App\Lib\Common\Util;


class GenerateRandom
{
    /**
     * 生成指定长度的随机字符串
     * @param int $length
     * @return bool|string
     */
    public static function generateRandom($length = 16)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }

        // sha1()将生成的随机字符串哈希
        return substr(sha1($string), 0, $length);
    }


    /**
     * 根据id创建不会重复的随机数
     * @param $id
     * @return string
     */
    public static function generateUnique($id)
    {
        $str = $id . time() . rand(1000, 9999);
        return md5($str);
    }
}
