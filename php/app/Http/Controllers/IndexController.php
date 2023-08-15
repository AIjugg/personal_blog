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
use App\Lib\Common\Util\ApiResponse;
use Illuminate\Routing\Controller as BaseController;

class IndexController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $data = (new MottoService())->getLastMotto();

            $result = ApiResponse::buildResponse($data);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }
}
