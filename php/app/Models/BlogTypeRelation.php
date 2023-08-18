<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-18
 * Time: 16:24
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class BlogTypeRelation extends Model
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
     * 删除关联关系
     * @param $condition
     * @return int
     */
    public function deleteBlogTypeRelation($condition)
    {
        $res = DB::table($this->table)->where('type_id', $condition['type_id'])
            ->where('blog_id', $condition['blog_id'])
            ->delete();

        return $res;
    }
}
