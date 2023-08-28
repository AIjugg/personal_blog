<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-16
 * Time: 18:24
 */

namespace App\Lib\Common\Constant;


class SystemEnum
{
    const USER_STATE_NORMAL = 1;
    const USER_STATE_EXAMINE = 2;
    const USER_STATE_FORBID = 3;
    const USER_STATE_CLOSED = 4;


    const USER_SEX_MALE = 1;
    const USER_SEX_FEMALE = 2;
    const USER_SEX_UNKNOWN = 3;

    private static $sexMap = [
        self::USER_SEX_MALE => '男',
        self::USER_SEX_FEMALE => '女',
        self::USER_SEX_UNKNOWN => '未知',
    ];

    /**
     * 获取性别
     * @param $sex
     * @return mixed
     */
    public static function getSex($sex)
    {
        return self::$sexMap[$sex];
    }
}
