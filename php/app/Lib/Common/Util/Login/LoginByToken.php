<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-16
 * Time: 20:47
 */

namespace App\Lib\Common\Util\Login;

use App\Lib\Common\Util\CommonException;
use App\Lib\Common\Util\ErrorCodes;
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
     * @return bool|string
     * @throws CommonException
     */
    public function storageLogin(array $userinfo)
    {
        $token = GenerateRandom::generateRandom();

        $value = json_encode($userinfo, JSON_UNESCAPED_UNICODE);
        $res = Redis::set(self::USERKEY_PREFIX . $token, $value);
        if (!$res) {
            throw new CommonException(ErrorCodes::REDIS_SAVE_ERROR);
        }

        Redis::expire(self::USERKEY_PREFIX . $token, self::EXPIRE_TIME);

        return $token;
    }


    public function checkLogin($token)
    {
        $value = Redis::get(self::USERKEY_PREFIX . $token);
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
     * @return mixed
     * @throws CommonException
     */
    public function clearLogin($token)
    {
        $res = Redis::del($token);
        if (!$res) {
            throw new CommonException(ErrorCodes::REDIS_DEL_ERROR);
        }
        return $res;
    }
}
