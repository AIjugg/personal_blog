<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-19
 * Time: 21:25
 */

namespace Illuminate\Support\Facades;


class GlobalValue extends Facade
{
    /**
     * 设置一个全局变量方法
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'global_value';
    }
}
