<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-09
 * Time: 14:15
 */

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * laravel的软删除并不是那么好用，我试着自己做一个
 * Class BaseModel
 * @package App\Models
 */
class BaseModel extends Model
{
    /**
     * SoftDeletes软删除的原理是定义了performDeleteOnModel方法覆盖基类Model中的该方法
     * 于是在对模型实例使用delete方法、destroy方法(实际上是对多个模型实例使用delete方法)时，不会真的删除而是更新deleted_at（默认标志软删除的字段）
     * 但这里有个问题，那就是使用DB::table($this->table)->where()->delete()时，调用的并不是Model的delete方法，而是Illuminate\Database\Query\Builder中的。
     */

    use SoftDeletes;

    const DELETED_AT = 'is_deleted';



}
