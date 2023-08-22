<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-19
 * Time: 19:47
 */

namespace App\Http\Middleware;

use App\Lib\Common\Util\CommonException;
use App\Lib\Common\Util\ErrorCodes;
use Closure;
use App\Lib\Common\Util\ApiResponse;
use Illuminate\Support\Facades\App;
use App\Lib\Common\Util\Login\LoginAccount;

class CheckLogin
{
    public function handle($request, Closure $next)
    {
        try {
            $session = App::make('session');
            $cookie = App::make('cookie');
            $userInfo = (new LoginAccount())->checkLogin($request,$session,$cookie);

            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }

            return $next($request);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }
}
