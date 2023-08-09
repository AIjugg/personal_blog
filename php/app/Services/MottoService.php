<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-09
 * Time: 14:19
 */

namespace App\Services;

use App\Models\Motto;

class MottoService
{
    public function getLastMotto()
    {
        $result = (new Motto())->getMotto();

        return $result;
    }
}
