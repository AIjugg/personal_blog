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
     * @param $sort
     * @param $offset
     * @param $limit
     * @return array
     */
    public function getBlog($condition, $sort, $offset, $limit)
    {
        $blog = DB::table($this->table);

        if (!empty($condition['uid'])) {
            $blog->where('uid', $condition['uid']);
        }
        if (!empty($condition['blog_id'])) {
            $where = is_array($condition['blog_id']) ? 'whereIn' : 'where';
            $blog->$where('blog_id', $condition['blog_id']);
        }
        if (!empty($condition['state'])) {
            $blog->where('state', $condition['state']);
        }

        if (!empty($sort['sort_field']) && !empty($sort['sort_direction'])) {
            $blog->orderBy($sort['sort_field'], $sort['sort_direction']);
        } else {
            $blog->orderBy('top', 'desc')
                ->orderBy('created_at', 'desc');
        }


        if (!empty($page) && !empty($pageSize)) {
            $blog->offset($offset)
                ->limit($limit);
        }

        $result = $blog->get()->toArray();

        return $result;
    }


    /**
     * 新增博客
     * @param $data
     * @return bool
     */
    public function addBlog($data)
    {
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
            $blogQuery->where('blog_id', $condition['uid']);
        }

        $res = $blogQuery->update($data);

        return $res;
    }


    public function updateBlog($data)
    {

    }
}
