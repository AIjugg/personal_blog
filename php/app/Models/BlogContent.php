<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-11
 * Time: 18:05
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BlogContent extends Model
{
    protected $table = 'blog_content';

    public function getContent($condition)
    {
        $contentQuery = DB::table($this->table);

        if (!empty($condition['blog_id'])) {
            $contentQuery->where('blog_id', $condition['blog_id']);
        }

        $content = $contentQuery->get()->toArray();

        return $content;
    }


    /**
     * 获取博客内容
     * @param $blogId
     * @return array
     */
    public function getBlogAndContent($blogId)
    {
        $blogDetail = DB::table('blog', 'b')->join('blog_content as bc', 'b.blog_id', '=', 'bc.blog_id')
            ->select(['b.blog_id', 'b.uid', 'b.title', 'b.like', 'b.comment', 'b.pageviews', 'bc.content', 'b.created_at'])
            ->where('b.blog_id', $blogId)
            ->first();

        return (array)$blogDetail;
    }


    /**
     * 新增博客内容
     * @param $data
     * @return bool
     */
    public function addContent($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());
        $res = DB::table($this->table)->insert($data);

        return $res;
    }


    /**
     * 编辑博客内容
     * @param $blogId
     * @param $content
     * @return int
     */
    public function editContent($blogId, $content)
    {
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $res = DB::table($this->table)->where('blog_id', $blogId)
            ->update(['content' => $content]);

        return $res;
    }
}
