<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-10
 * Time: 17:50
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Blog extends Model
{
    protected $table = 'blog';

    /**
     * 获取博客列表
     * @param $condition
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getBlogQuery($condition)
    {
        $blogQuery = DB::table($this->table, 'b')->where('b.is_deleted', 0);

        if (!empty($condition['uid'])) {
            $blogQuery->where('b.uid', $condition['uid']);
        }
        if (!empty($condition['title'])) {
            $blogQuery->where('b.title', 'like', '%' . $condition['title'] . '%');
        }
        if (!empty($condition['blog_id'])) {
            $where = is_array($condition['blog_id']) ? 'whereIn' : 'where';
            $blogQuery->$where('b.blog_id', $condition['blog_id']);
        }
        if (!empty($condition['state'])) {
            $blogQuery->where('b.state', $condition['state']);
        }
        if (!empty($condition['type_id'])) {
            $where = is_array($condition['type_id']) ? 'whereIn' : 'where';

            $blogQuery->join('blog_type_relation as br', 'br.blog_id', '=', 'b.blog_id')
                ->$where('br.type_id', $condition['type_id']);
        }

        return $blogQuery;
    }


    public function getBlog($condition, $sortArr, $pageSet)
    {
        $blogQuery = $this->getBlogQuery($condition);

        if (!empty($sortArr['sort_field']) && !empty($sortArr['sort_direction'])) {
            $blogQuery->orderBy($sortArr['sort_field'], $sortArr['sort_direction']);
        } else {
            $blogQuery->orderBy('b.top', 'desc')
                ->orderBy('b.created_at', 'desc');
        }

        if (isset($pageSet['offset']) && !empty($pageSet['limit'])) {
            $blogQuery->offset($pageSet['offset'])
                ->limit($pageSet['limit']);
        }

        $result = $blogQuery->get()->toArray();
        return $result;
    }


    /**
     * 统计博客数量
     * @param $condition
     * @return integer
     */
    public function countBlog($condition)
    {
        $blogQuery = $this->getBlogQuery($condition);

        $result = $blogQuery->count();

        return $result;
    }


    /**
     * 新增博客
     * @param $data
     * @return bool
     */
    public function addBlog($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());
        $res = DB::table($this->table)->insert($data);

        return $res;
    }


    /**
     * 新增博客
     * @param $data
     * @return int
     */
    public function addBlogGetId($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());

        $blogId = DB::table($this->table)->insertGetId($data);

        return $blogId;
    }


    /**
     * @param $data
     * @param $condition
     * @return int
     */
    public function editBlog($data, $condition)
    {
        $blogQuery = DB::table($this->table);

        if (!empty($condition['blog_id'])) {
            $blogQuery->where('blog_id', $condition['blog_id']);
        }
        if (!empty($condition['uid'])) {
            $blogQuery->where('uid', $condition['uid']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $res = $blogQuery->update($data);

        return $res;
    }
}
