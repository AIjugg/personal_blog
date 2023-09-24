<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-18
 * Time: 16:24
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;

class BlogTypeRelation extends BaseModel
{
    protected $table = 'blog_type_relation';

    /**
     * 博客关联分类
     * @param $data
     * @return int
     */
    public function addBlogTypeRelation($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());

        $res = DB::table($this->table)->insertGetId($data);

        return $res;
    }


    /**
     * 博客关联分类
     * @param $data
     * @return int
     */
    public function addMultiBlogTypeRelation($data)
    {
        $now = date('Y-m-d H:i:s', time());

        foreach ($data as &$v) {
            $v['created_at'] = $v['updated_at'] = $now;
        }

        $res = DB::table($this->table)->insert($data);

        return $res;
    }


    /**
     * 删除关联关系
     * 这玩意儿留着没意义
     * @param $condition
     * @return int
     */
    public function deleteBlogTypeRelation($condition)
    {
        $query = DB::table($this->table);

        if (!empty($condition['type_id'])) {
            $query->where('type_id', $condition['type_id']);
        }

        if (!empty($condition['blog_id'])) {
            $query->where('blog_id', $condition['blog_id']);
        }

        // $res = $query->update(['is_deleted' => 1]);
        $res = $query->delete();

        return $res;
    }


    /**
     * 获取博客分类关联关系
     * @param $condition
     * @return array
     */
    public function getBlogTypeRelation($condition)
    {
        $query = DB::table($this->table);

        if (!empty($condition['type_id'])) {
            $query->where('type_id', $condition['type_id']);
        }

        if (!empty($condition['blog_id'])) {
            $where = is_array($condition['blog_id']) ? "whereIn" : "where";

            $query->$where('blog_id', $condition['blog_id']);
        }

        $res = $query->where('is_deleted', 0)
            ->get()->map(function ($value) { return (array)$value; })->toArray();

        return $res;
    }



    /**
     * 获取博客分类关联关系
     * @param $condition
     * @return array
     */
    public function blogTypeRelation($condition)
    {
        $query = DB::table($this->table, 'r')->select(['r.relation_id','r.blog_id','r.type_id','t.type_name']);

        if (!empty($condition['blog_id'])) {
            $where = is_array($condition['blog_id']) ? "whereIn" : "where";

            $query->$where('r.blog_id', $condition['blog_id']);
        }

        $res = $query->leftJoin('blog_type as t', 't.type_id', '=', 'r.type_id')
            ->where('r.is_deleted', 0)
            ->get()->map(function ($value) { return (array)$value; })->toArray();

        return $res;
    }
}
