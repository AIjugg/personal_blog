<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-11
 * Time: 19:42
 */

namespace App\Services;

use App\Lib\Common\Util\CommonException;
use App\Models\BlogType;
use App\Lib\Common\Util\ErrorCodes;


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
            throw new CommonException(ErrorCodes::BLOG_TYPE_DELETE_FAIL);
        }
        return [];
    }
}
