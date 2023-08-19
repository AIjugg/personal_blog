<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-19
 * Time: 19:47
 */

namespace App\Http\Middleware;

use App\Services\UserService;
use Closure;
use App\Lib\Common\Util\ApiResponse;
use Illuminate\Support\Facades\App;

class CheckLogin
{
    public function handle($request, Closure $next)
    {
        try {
            $remberInfo = (new UserService())->checkUserLogin();


            return $next($request);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }
}
