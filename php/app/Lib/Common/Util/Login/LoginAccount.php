<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-22
 * Time: 21:09
 */

namespace App\Lib\Common\Util\Login;

use Illuminate\Cookie\CookieJar;
use Illuminate\Session\Store;
use Illuminate\Http\Request;


class LoginAccount
{
    protected $loginKey = 'cy_login';

    protected $expireTime;


    public function __construct()
    {
        // 取了session过期时间
        $this->expireTime = config('session')['lifetime'] ?? 120;
    }


    /**
     * 存储用户登录信息到session，并且set-cookie返回给浏览器
     * 其实$session和$cookie可以直接用App::make(...)在方法内创建，现在这么写只是为了方法跳转方便看起来舒服
     * @param array $user
     * @param Store $session
     * @param CookieJar $cookie
     * @return bool
     */
    public function storageLogin($user, $session, $cookie)
    {
        $json = json_encode($user, JSON_UNESCAPED_UNICODE);

        // 将用户信息存储到session
        $session->put($this->loginKey, $json);
        // 刷新session_id，不然其他用户登录也是同一个session_id
        $session->migrate(true);

        // 这就等于set-cookie操作
        $cookie->queue($this->loginKey, $json, $this->expireTime);

        return true;
    }


    /**
     * 利用cookie session原理的登录方式
     * @param Store $session
     * @param CookieJar $cookie
     * @return array|mixed
     */
    public function checkLogin($session, $cookie)
    {
//        $cookie = App::make('cookie');
//        $session = App::make('session');

        $json = $session->get($this->loginKey);
        if (!empty($json)) {
            $userInfo = json_decode($json, true);
        }

        return $userInfo ?? [];


        // 在找不到相应session时，是否需要根据cookie再生成session？
//        $userInfo = [];
//        $cookieJson = $cookie->queued($this->loginKey);
//        if ($cookieJson) {
//            if ($cookieJson instanceof Cookie) {
//                $cookieJson = $cookieJson->getValue();
//                $userInfo = json_decode($cookieJson, true);
//            }
//        } else {
//            $cookieJson = $request->cookie($this->loginKey);
//            if ($cookieJson) {
//                $userInfo = json_decode($cookieJson, true);
//            }
//        }
//
//        $request->offsetSet('user_info', $userInfo);
//        return $userInfo;
    }


    /**
     * 清除登录信息
     * @param Store $session
     * @param CookieJar $cookie
     * @return array
     */
    public function clearLogin($session, $cookie)
    {
        $session->pull($this->loginKey);

        // 将cookie设置为过期
        $cookie->expire($this->loginKey);

        return [];
    }
}
