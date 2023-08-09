<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-09
 * Time: 14:10
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Motto extends Model
{
    public function getMotto()
    {
        $data = DB::table('motto')->select('img','motto')
            ->orderBy("id",'desc')
            ->limit(1)
            ->get()->toArray();

        return $data;
    }
}
