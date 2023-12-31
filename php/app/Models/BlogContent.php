<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-11
 * Time: 18:05
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;

class BlogContent extends BaseModel
{
    protected $table = 'blog_content';

    public function getContent($condition)
    {
        $contentQuery = DB::table($this->table);

        if (!empty($condition['blog_id'])) {
            $where = is_array($condition['blog_id']) ? 'whereIn' : 'where';
            $contentQuery->$where('blog_id', $condition['blog_id']);
        }

        return $contentQuery->get()->map(function ($value) { return (array)$value; })
            ->toArray();
    }


    /**
     * 获取博客内容
     * @param array $condition
     * @param array $selectField
     * @return array
     */
    public function getBlogAndContent($condition, $selectField = [])
    {
        $selectField = !empty($selectField) ? $selectField : ['b.blog_id', 'b.uid', 'b.title', 'b.like', 'b.comment', 'b.pageviews', 'bc.content', 'b.created_at'];

        $query = DB::table('blog', 'b')->join('blog_content as bc', 'b.blog_id', '=', 'bc.blog_id');

        if (!empty($condition['blog_id'])) {
            $query->where('b.blog_id', $condition['blog_id']);
        }

        if (!empty($condition['uid'])) {
            $query->where('b.uid', $condition['uid']);
        }

        $blogDetail = $query->select($selectField)
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
     * @param array $data
     * @return int
     */
    public function editContent($blogId, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $res = DB::table($this->table)->where('blog_id', $blogId)
            ->update($data);

        return $res;
    }
}
