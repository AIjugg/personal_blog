<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-16
 * Time: 21:56
 */

namespace App\Lib\Common\Util;

/**
 * 抽象出一个登录token存储类，便于使用不同存储方式存token
 * Class LoginBase
 * @package App\Lib\Common\Util
 */
abstract class LoginBase
{
    // 存储用户token
    abstract public function setUserToken(array $userinfo);

    // 根据token获取用户信息
    abstract public function getUserinfoByToken($token);

    // 持久化token的存储时间
    abstract public function expireToken($token, $time);

    // 登出
    abstract public function delToken($token);
}
