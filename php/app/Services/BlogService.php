<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-12
 * Time: 20:21
 */

namespace App\Services;

use App\Models\Blog;
use App\Models\BlogContent;
use Illuminate\Support\Facades\DB;
use App\Lib\Common\Util\CommonException;

class BlogService
{
    /**
     * 获取博客详情
     * @param $blogId
     * @return mixed
     * @throws CommonException
     */
    public function getBlogDetail($blogId)
    {
        $result = (new BlogContent())->getBlogAndContent($blogId);

        if (empty($result)) {
            throw new CommonException('找不到相应博客', 1005);
        } else {
            return $result[0];
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
            'content_simple' => $data['content_simple']
        ];

        try {
            DB::beginTransaction();
            // 先插入博客获取博客Id
            $blogId = (new Blog())->addBlogGetId($blogData);
            if (empty($blogId)) {
                throw new CommonException('创建博客失败', 1002);
            }

            $blogContent = [
                'blog_id' => $data['blog_id'],
                'content' => $data['content']
            ];

            // 插入博客内容
            $res = (new BlogContent())->addContent($blogContent);
            if (!$res) {
                throw new CommonException('插入博客内容失败', 1006);
            }

            DB::commit();
            return [];
        } catch (\Exception $e) {
            // 记录错误
            // log($e->getCode(), $e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * @param $uid
     * @param $data
     * @return array
     * @throws CommonException
     */
    public function editBlog($uid, $data)
    {
        if (empty($data['blog_id'])) {
            throw new CommonException('找不到相关博客', 1001);
        }

        $blogId = $data['blog_id'];

        try {
            DB::beginTransaction();
            $res = (new Blog())->editBlog($data, ['uid' => $uid, 'blog_id' => $blogId]);

            if (empty($res)) {
                throw new CommonException('编辑博客失败', 1001);
            }

            if (!empty($data['content'])) {
                $res2 = (new BlogContent())->editContent($blogId, $data['content']);
                if (!$res2) {
                    throw new CommonException('更新博客内容失败', 1007);
                }
            }

            DB::commit();

            return [];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
