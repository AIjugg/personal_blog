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
            ->get()->toArray();

        return $result;
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
}
