<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-11
 * Time: 19:39
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class BlogType extends Model
{
    use SoftDeletes;

    const DELETED_AT = 'deleted_at';

    protected $table = 'blog_type';


    /**
     * 获取类型列表
     * @return array
     */
    public function getBlogType()
    {
        $result = DB::table($this->table)
            ->select(['type_id','type_name'])
            ->where('is_deleted', 0)
            ->get()->toArray();

        return $result;
    }


    /**
     * 类型数量
     * @return integer
     */
    public function countBlogType()
    {
        $total = DB::table($this->table)
            ->where('is_deleted', 0)
            ->count();

        return $total;
    }


    /**
     * 软删除类型
     * @param $typeId
     * @return int
     */
    public function deleteBlogType($typeId)
    {
        $result = DB::table($this->table)
            ->where('type_id', $typeId)
            ->delete();

        return $result;
    }


    /**
     * 增加博客类型
     * @param $data
     * @return int
     */
    public function addBlogType($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());

        $result = DB::table($this->table)
            ->insertGetId($data);

        return $result;
    }


    /**
     * 编辑博客分类
     * @param $typeId
     * @param $data
     * @return int
     */
    public function editBlogType($typeId, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $result = DB::table($this->table)
            ->where('type_id', $typeId)
            ->update($data);

        return $result;
    }
}
