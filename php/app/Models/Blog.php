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
     * @param $sortArr
     * @param $pageSet
     * @return array
     */
    public function getBlog($condition, $sortArr, $pageSet)
    {
        $blog = DB::table($this->table, 'b');

        if (!empty($condition['uid'])) {
            $blog->where('b.uid', $condition['uid']);
        }
        if (!empty($condition['title'])) {
            $blog->where('b.title', 'like', '%' . $condition['title'] . '%');
        }
        if (!empty($condition['blog_id'])) {
            $where = is_array($condition['blog_id']) ? 'whereIn' : 'where';
            $blog->$where('b.blog_id', $condition['blog_id']);
        }
        if (!empty($condition['state'])) {
            $blog->where('b.state', $condition['state']);
        }
        if (!empty($condition['type_id'])) {
            $where = is_array($condition['type_id']) ? 'whereIn' : 'where';

            $blog->join('blog_type_relation as br', 'br.blog_id', '=', 'b.blog_id')
                ->$where('br.type_id', $condition['type_id']);
        }

        if (!empty($sortArr['sort_field']) && !empty($sortArr['sort_direction'])) {
            $blog->orderBy($sortArr['sort_field'], $sortArr['sort_direction']);
        } else {
            $blog->orderBy('b.top', 'desc')
                ->orderBy('b.created_at', 'desc');
        }

        if (isset($pageSet['offset']) && !empty($pageSet['limit'])) {
            $blog->offset($pageSet['offset'])
                ->limit($pageSet['limit']);
        }

        $result = $blog->get()->toArray();

        return $result;
    }


    /**
     * 统计博客数量
     * @param $condition
     * @return integer
     */
    public function countBlog($condition)
    {
        $blog = DB::table($this->table);

        if (!empty($condition['uid'])) {
            $blog->where('uid', $condition['uid']);
        }
        if (!empty($condition['title'])) {
            $blog->where('title', 'like', '%' . $condition['title'] . '%');
        }
        if (!empty($condition['blog_id'])) {
            $where = is_array($condition['blog_id']) ? 'whereIn' : 'where';
            $blog->$where('blog_id', $condition['blog_id']);
        }
        if (!empty($condition['state'])) {
            $blog->where('state', $condition['state']);
        }

        $result = $blog->count();

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
            $blogQuery->where('blog_id', $condition['uid']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $res = $blogQuery->update($data);

        return $res;
    }
}
