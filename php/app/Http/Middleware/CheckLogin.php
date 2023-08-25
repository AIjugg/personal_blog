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
use App\Lib\Common\Util\Login\LoginByToken;
use Closure;
use App\Lib\Common\Util\ApiResponse;
use Illuminate\Support\Facades\App;
use App\Lib\Common\Util\Login\LoginAccount;

class CheckLogin
{
    public function handle($request, Closure $next)
    {
        try {
            // 检测是否是用token登录
            $accessTokenHeader = $request->header('Authorization');
            if (!empty($accessTokenHeader)) {
                $accessToken = substr($accessTokenHeader, 7);
                $userInfo = (new LoginByToken())->checkLogin($accessToken);
            }

            if (empty($userInfo)) {
                $session = App::make('session');
                $cookie = App::make('cookie');
                $userInfo = (new LoginAccount())->checkLogin($session, $cookie);
            }

            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }

            $request->offsetSet('user_info', $userInfo);
            return $next($request);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }
}
