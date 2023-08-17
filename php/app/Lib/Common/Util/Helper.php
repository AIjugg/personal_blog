<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-17
 * Time: 14:35
 */

namespace App\Lib\Common\Util;


class Helper
{
    /**
     * 标准化排序
     * @param $input
     * @return array
     */
    public static function sortStandard($input)
    {
        $sortArr = [];
        if (!empty($input['sort_filed'])) {
            $sortArr['sort_filed'] = $input['sort_filed'];
        }
        if (!empty($input['sort_direction'])) {
            $sortArr['sort_direction'] = $input['sort_direction'];
        }

        if (!empty($sortArr['sort_filed']) && empty($sortArr['sort_direction'])) {
            $sortArr['sort_direction'] = 'desc';
        }

        return $sortArr;
    }


    /**
     * 标准化分页
     * @param $input
     * @return array
     */
    public static function pageStandard($input)
    {
        $pageSet = [];

        if (!empty($input['pagesize'])) {
            $pageSet['limit'] = $input['pagesize'];
        }

        if (!empty($input['pagesize']) && !empty($input['page'])) {
            $pageSet['offset'] = $input['pagesize'] * ($input['page'] - 1);
        }

        return $pageSet;
    }
}
