<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-11
 * Time: 19:42
 */

namespace App\Services;

use App\Exceptions\CommonException;
use App\Models\BlogType;


class BlogTypeService
{
    public function listBlogType()
    {
        $res = (new BlogType())->getBlogType();

        return $res;
    }


    /**
     * @param $typeId
     * @return array|bool
     * @throws CommonException
     */
    public function deleteBlogType($typeId)
    {
        if (empty($typeId)) {
            return false;
        }

        $blogType = new BlogType();

        $res = $blogType->deleteBlogType($typeId);

        if (!$res) {
            throw new CommonException('删除失败', 1003);
        }
        return [];
    }
}
