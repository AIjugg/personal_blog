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
     const BLOG_TYPE_NAME_EMPTY = 12007;
     const BLOG_TYPE_ADD_FAIL = 12008;
     const BLOG_TYPE_EDIT_FAIL = 12009;
     const BLOG_TYPE_RELATION_FAIL = 12010;
    const BLOG_TYPE_RELATION_DELETE_FAIL = 12011;

     // 用户
    const USER_PWD_WRONG = 13001;
    const USER_PWD_REPEAT_WRONG = 13002;
    const USER_EXIST = 13003;
    const USERNAME_WRONG = 13004;
    const ADD_USER_WRONG = 13005;
    const EDIT_USER_WRONG = 13006;
    const USER_TOKEN_STORAGE_WRONG = 13007;

    private static $codeMap = [
        self::PARAM_ERROR => '参数错误',
        self::UNKNOWN_ERROR => '未知错误',

        self::BLOG_NOT_EXIST => '博客不存在',
        self::BLOG_ADD_FAIL => '创建博客失败',
        self::BLOG_EDIT_FAIL => '编辑博客失败',
        self::BLOG_TYPE_DELETE_FAIL => '博客分类删除失败',
        self::BLOG_CONTENT_ADD_FAIL => '博客内容创建失败',
        self::BLOG_CONTENT_EDIT_FAIL => '博客内容编辑失败',
        self::BLOG_TYPE_NAME_EMPTY => '博客分类名非空',
        self::BLOG_TYPE_ADD_FAIL => '博客分类增加失败',
        self::BLOG_TYPE_EDIT_FAIL => '博客分类编辑失败',
        self::BLOG_TYPE_RELATION_FAIL => '博客关联分类失败',
        self::BLOG_TYPE_RELATION_DELETE_FAIL => '删除博客关联分类失败',

        self::USER_PWD_WRONG => '用户密码错误',
        self::USER_PWD_REPEAT_WRONG => '两次密码不一致',
        self::USER_EXIST => '用户名已存在',
        self::USERNAME_WRONG => '用户名不规范请使用纯字母',
        self::ADD_USER_WRONG => '创建用户失败',
        self::EDIT_USER_WRONG => '编辑用户失败',
        self::USER_TOKEN_STORAGE_WRONG => 'token存储失败',
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
