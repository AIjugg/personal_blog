<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-16
 * Time: 20:47
 */

namespace App\Lib\Common\Util\Login;

use Illuminate\Support\Facades\Redis;
use App\Lib\Common\Util\GenerateRandom;

/**
 * Redis方法 @see \Illuminate\Redis\Connections\PhpRedisConnection
 * Class Login
 * @package App\Lib\Common\Util\Redis
 */
class LoginByToken
{
    const USERKEY_PREFIX = 'user';

    const EXPIRE_TIME = 86400;

    /**
     * 存储token
     * @param array $userinfo
     * @return mixed
     */
    public function storageLogin(array $userinfo)
    {
        $token = GenerateRandom::generateRandom();

        $value = json_encode($userinfo, JSON_UNESCAPED_UNICODE);
        $res = Redis::set(self::USERKEY_PREFIX . $token, $value);
        Redis::expire(self::USERKEY_PREFIX . $token, self::EXPIRE_TIME);

        return $res;
    }


    public function checkLogin($token)
    {
        $value = Redis::get($token);
        if (!empty($value)) {
            $userinfo = json_decode($value, true);
        }

        return $userinfo ?? [];
    }


    /**
     * 延长token时间
     * @param $token
     * @param int $time
     * @return bool
     */
    public function expireToken($token, $time = self::EXPIRE_TIME)
    {
        if (!Redis::exists($token)) {
            return false;
        }

        Redis::expire($token, $time);

        return true;
    }

    /**
     * 登出
     * @param $token
     * @return bool
     */
    public function clearLogin($token)
    {
        Redis::del($token);

        return true;
    }
}
