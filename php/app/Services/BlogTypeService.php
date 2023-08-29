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
use App\Models\BlogTypeRelation;


class BlogTypeService
{
    public function listBlogType()
    {
        $res = (new BlogType())->getBlogType();

        return $res;
    }


    public function countBlogType()
    {
        $res = (new BlogType())->countBlogType();

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


    /**
     * 新增博客分类
     * @param $typeName
     * @return array
     * @throws CommonException
     */
    public function addBlogType($typeName)
    {
        if (empty($typeName)) {
            throw new CommonException(ErrorCodes::BLOG_TYPE_NAME_EMPTY);
        }

        $data = [
            'type_name' => $typeName
        ];

        $blogType = new BlogType();
        $res = $blogType->addBlogType($data);

        if (empty($res)) {
            throw new CommonException(ErrorCodes::BLOG_TYPE_ADD_FAIL);
        }
        return ['type_id' => $res];
    }


    /**
     * 编辑博客分类
     * @param $typeId
     * @param $typeName
     * @return array
     * @throws CommonException
     */
    public function editBlogType($typeId, $typeName)
    {
        $data = [
            'type_name' => $typeName
        ];

        $blogType = new BlogType();
        $res = $blogType->editBlogType($typeId, $data);

        if (empty($res)) {
            throw new CommonException(ErrorCodes::BLOG_TYPE_ADD_FAIL);
        }
        return [];
    }


    /**
     * 关联博客和分类
     * @param $blogId
     * @param $typeId
     * @return array
     * @throws CommonException
     */
    public function relationBlogType($blogId, $typeId)
    {
        $data = [
            'type_id' => $typeId,
            'blog_id' => $blogId
        ];
        $res = (new BlogTypeRelation())->addBlogTypeRelation($data);

        if (empty($res)) {
            throw new CommonException(ErrorCodes::BLOG_TYPE_RELATION_FAIL);
        }
        return [];
    }


    /**
     * 删除博客的分类
     * @param $blogId
     * @param $typeId
     * @return array
     * @throws CommonException
     */
    public function delRelationBlogType($blogId, $typeId)
    {
        $condition = [];

        if (!empty($blogId)) {
            $condition['blog_id'] = $blogId;
        }

        if (!empty($typeId)) {
            $condition['type_id'] = $typeId;
        }

        if (empty($condition)) {
            return [];
        }
        $res = (new BlogTypeRelation())->deleteBlogTypeRelation($condition);

        if (empty($res)) {
            throw new CommonException(ErrorCodes::BLOG_TYPE_RELATION_DELETE_FAIL);
        }
        return [];
    }


    /**
     * 获取博客分类关联关系
     * @param int $typeId
     * @param int $blogId
     * @return array
     */
    public function getRelationBlogType($typeId = 0, $blogId = 0)
    {
        $condition = [];

        if (!empty($typeId)) {
            $condition['type_id'] = $typeId;
        }

        if (!empty($blogId)) {
            $condition['blog_id'] = $blogId;
        }

        if (empty($condition)) {
            return [];
        }

        $res = (new BlogTypeRelation())->getBlogTypeRelation($condition);

        return $res;
    }
}
