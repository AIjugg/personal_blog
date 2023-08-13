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
            ->select(['b.blog_id', 'b.uid', 'b.title', 'b.like', 'b.comment', 'b.pageviews', 'bc.content'])
            ->where('b.blog_id', $blogId)
            ->get()->toArray();

        return $blogDetail;
    }


    /**
     * 新增博客内容
     * @param $data
     * @return bool
     */
    public function addContent($data)
    {
        $res = DB::table($this->table)->insert($data);

        return $res;
    }
}
