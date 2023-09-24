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
use Illuminate\Support\Facades\Log;


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

//    use SoftDeletes;
//
//    const DELETED_AT = 'is_deleted';

    /**
     * 批量更新
     *
     * @param string $tableName 表名称
     * @param string $pk 更新的字段
     * @param array $multipleData 要更新的数据
     * @return bool|int
     */
    public function updateBatch($tableName, string $pk = 'id', array $multipleData = [])
    {
        try {
            if (empty($multipleData)) {
                Log::info("批量更新数据为空");
                return false;
            }

            $firstRow = current($multipleData);

            $updateColumn = array_keys($firstRow);
            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $referenceColumn = !isset($firstRow[$pk]) ? 'id' : current($updateColumn);
            unset($updateColumn[0]);
            // 拼接sql语句
            $updateSql = "UPDATE " . $tableName . " SET ";
            $sets = [];
            $bindings = [];
            foreach ($updateColumn as $uColumn) {
                $setSql = "`" . $uColumn . "` = CASE ";
                foreach ($multipleData as $data) {
                    $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn];
                }
                $setSql .= "ELSE `" . $uColumn . "` END ";
                $sets[] = $setSql;
            }
            $updateSql .= implode(', ', $sets);
            $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings = array_merge($bindings, $whereIn);
            $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
            $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
            Log::info($updateSql);
            // 传入预处理sql语句和对应绑定数据
            return DB::update($updateSql, $bindings);
        } catch (\Exception $e) {
            Log::info("批量更新数据失败：" . $e->getMessage());
            return false;
        }
    }


}
