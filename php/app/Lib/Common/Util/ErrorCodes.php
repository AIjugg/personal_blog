<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-15
 * Time: 19:40
 */

namespace App\Lib\Common\Util;


class ErrorCodes
{
    // 常规
    const PARAM_ERROR = 10001;
    const UNKNOWN_ERROR = 10002;

    // 博客
     const BLOG_NOT_EXIST = 12001;
     const BLOG_ADD_FAIL = 12002;
     const BLOG_EDIT_FAIL = 12003;
     const BLOG_TYPE_DELETE_FAIL = 12004;
     const BLOG_CONTENT_ADD_FAIL = 12005;
     const BLOG_CONTENT_EDIT_FAIL = 12006;

     // 用户
    const USER_PWD_WRONG = 13001;

    private static $codeMap = [
        self::PARAM_ERROR => '参数错误',
        self::UNKNOWN_ERROR => '未知错误',

        self::BLOG_NOT_EXIST => '博客不存在',
        self::BLOG_ADD_FAIL => '创建博客失败',
        self::BLOG_EDIT_FAIL => '编辑博客失败',
        self::BLOG_TYPE_DELETE_FAIL => '博客分类删除失败',
        self::BLOG_CONTENT_ADD_FAIL => '博客内容创建失败',
        self::BLOG_CONTENT_EDIT_FAIL => '博客内容编辑失败',

        self::USER_PWD_WRONG => '用户密码错误'
    ];


    /**
     * 获取错误码相应错误信息
     * @param $code
     * @return mixed|string
     */
    public static function getCodeMessage($code)
    {
        return self::$codeMap[$code] ?? '系统错误';
    }

}
