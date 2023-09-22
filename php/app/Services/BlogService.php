<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-12
 * Time: 20:21
 */

namespace App\Services;

use App\Lib\Common\Util\ErrorCodes;
use App\Models\Blog;
use App\Models\BlogContent;
use Illuminate\Support\Facades\DB;
use App\Lib\Common\Util\CommonException;

class BlogService
{
    /**
     * 获取博客详情
     * @param array $condition
     * @param array $selectField
     * @return mixed
     * @throws CommonException
     */
    public function getBlogDetail($condition, $selectField = [])
    {
        $result = (new BlogContent())->getBlogAndContent($condition, $selectField);

        if (empty($result)) {
            throw new CommonException(ErrorCodes::BLOG_NOT_EXIST);
        } else {
            return $result;
        }
    }


    /**
     * @param $uid
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function addBlog($uid, $data)
    {
        $blogData = [
            'uid' => $uid,
            'title' => $data['title'],
            'description' => $data['description'],
            'image' => $data['image'],
            'state' => $data['state'],
            'top' => $data['top']
        ];

        if (isset($data['created_at'])) {
            $blogData['created_at'] = $data['created_at'];
        }
        if (isset($data['updated_at'])) {
            $blogData['updated_at'] = $data['updated_at'];
        }

        try {
            DB::beginTransaction();
            // 先插入博客获取博客Id
            $blogId = (new Blog())->addBlogGetId($blogData);
            if (empty($blogId)) {
                throw new CommonException(ErrorCodes::BLOG_ADD_FAIL);
            }

            $blogContent = [
                'blog_id' => $blogId,
                'content' => $data['content']
            ];

            // 插入博客内容
            $res = (new BlogContent())->addContent($blogContent);
            if (!$res) {
                throw new CommonException(ErrorCodes::BLOG_CONTENT_ADD_FAIL);
            }

            // 博客关联分类
            if (isset($data['type_ids'])) {
                (new BlogTypeService())->relationBlogType($blogId, $data['type_ids']);
            }

            DB::commit();
            return ['blog_id' => $blogId];
        } catch (\Exception $e) {
            // 记录错误
            // log($e->getCode(), $e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * 编辑博客
     * @param $uid
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function editBlog($uid, $data)
    {
        if (empty($data['blog_id'])) {
            throw new CommonException(ErrorCodes::PARAM_ERROR);
        }

        $blogData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'image' => $data['image'],
            'state' => $data['state'],
            'top' => $data['top']
        ];

        $blogContent = [
            'content' => $data['content']
        ];
        $blogId = $data['blog_id'];

        try {
            DB::beginTransaction();
            $res = (new Blog())->editBlog($blogData, ['uid' => $uid, 'blog_id' => $blogId]);

            if (empty($res)) {
                throw new CommonException(ErrorCodes::BLOG_EDIT_FAIL);
            }

            if (!empty($data['content'])) {
                $res2 = (new BlogContent())->editContent($blogId, $blogContent);
                if (!$res2) {
                    throw new CommonException(ErrorCodes::BLOG_CONTENT_EDIT_FAIL);
                }
            }

            // 博客关联分类
            (new BlogTypeService())->relationBlogType($data['blog_id'], $data['type_ids'] ?? []);
//            if (isset($data['type_ids'])) {
//                (new BlogTypeService())->relationBlogType($data['blog_id'], $data['type_ids']);
//            }

            DB::commit();
            return ['blog_id' => $blogId];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * 博客列表
     * @param $condition
     * @param array $sortArr
     * @param array $pageSet
     * @return array
     * @throws CommonException
     */
    public function listBlog($condition, $sortArr = [], $pageSet = [])
    {
        $result = (new Blog())->getBlog($condition, $sortArr, $pageSet);

        if (empty($result)) {
            throw new CommonException(ErrorCodes::BLOG_NOT_EXIST);
        } else {
            return $result;
        }
    }


    public function countBlog($condition)
    {
        $total = (new Blog())->countBlog($condition);

        return $total;
    }


    public function getBlog($condition)
    {
        $result = (new Blog())->getBlog($condition);

        return $result;
    }
}
