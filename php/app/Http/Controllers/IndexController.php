<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-09
 * Time: 14:26
 */

namespace App\Http\Controllers;

use App\Services\MottoService;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function IndexAction(Request $request)
    {
        $result = (new MottoService())->getLastMotto();

        return response($result);
    }
}
