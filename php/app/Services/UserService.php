<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-09
 * Time: 12:54
 */

namespace App\Services;

use App\Lib\Common\Util\CommonException;
use App\Lib\Common\Util\ErrorCodes;
use App\Lib\Common\Util\GenerateRandom;
use App\Lib\Common\Util\Redis\Login;
use App\Models\User;
use App\Lib\Common\Constant\SystemEnum;
use Illuminate\Session\Store;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserService
{
    protected $loginUtil;

    protected $loginKey = 'cy_login';

    protected $expireTime;

    public function __construct()
    {
        $this->loginUtil = new Login();

        // 取了session过期时间
        $this->expireTime = config('session')['lifetime'] ?? 120;
    }


    /**
     * 获取用户实例
     * @param $username
     * @param int $state
     * @param array $selectField
     * @return array
     */
    public function getUserByName($username, $state = SystemEnum::USER_STATE_NORMAL, $selectField = [])
    {
        $condition = ['username' => $username];
        if (!empty($state)) {
            $condition['state'] = $state;
        }

        $user = (new User())->getUserByCondition($condition, $selectField);

        return $user;
    }


    /**
     * 账号密码登录，存储token
     * @param $username
     * @param $password
     * @return integer
     * @throws CommonException
     */
    public function loginByAccount($username, $password)
    {
        $user = $this->getUserByName($username, SystemEnum::USER_STATE_NORMAL, ['id','username','email','mobile','password','nickname','state','last_login']);

        // 验证密码
        if (!$this->verifyPasswordBcrypt($password, $user['password'])) {
            throw new CommonException(ErrorCodes::USER_PWD_WRONG);
        }
        unset($user['password']);

        // session存储登录信息，cookie返回给客户端
        $session = App::make('session');
        $res = $this->storageLogin($user, $session);

        if (!$res) {
            throw new CommonException(ErrorCodes::USER_TOKEN_STORAGE_WRONG);
        }

        return $user['id'];
    }


    /**
     * 根据账号密码创建用户
     * @param $username
     * @param $password
     * @return array
     * @throws CommonException
     */
    public function registerByAccount($username, $password)
    {
        $encryption = $this->encryptPasswordBcrypt($password);

        $userData = [
            'username' => $username,
            'password' => $encryption,
        ];

        $res = (new User())->addUser($userData);
        if (empty($res)) {
            throw new CommonException(ErrorCodes::ADD_USER_WRONG);
        }

        return ['uid' => $res];
    }


    public function encryptPasswordBcrypt($password)
    {
        return Hash::make($password);
    }


    /**
     * Bcrypt算法加密验证密码
     * @param $password string 待验证的密码
     * @param $encryption string 存储的密文
     * @return bool
     */
    public function verifyPasswordBcrypt($password, $encryption)
    {
        if (Hash::check($password, $encryption)) {
            return true;
        }
        return false;
    }


    /**
     * 编辑用户
     * @param $uid
     * @param $data
     * @return array
     * @throws CommonException
     */
    public function editUser($uid, $data)
    {
        $res = (new User())->editUser($uid, $data);
        if (empty($res)) {
            throw new CommonException(ErrorCodes::EDIT_USER_WRONG);
        }

        return [];
    }


    // 存储用户登录token，做成依赖倒置，方便以后更改存储方式
    public function storageLogin2($user)
    {
        return $this->loginUtil->setUserToken($user);
    }


    /**
     * 存储用户登录信息到session，并且set-cookie返回给浏览器
     * @param $user
     * @param Store $session
     * @return bool
     */
    public function storageLogin($user, $session)
    {
        $json = json_encode($user, JSON_UNESCAPED_UNICODE);

        // 将用户信息存储到session
        $session->put($this->loginKey, $json);
        // 刷新session_id，不然其他用户登录也是同一个session_id
        $session->migrate(true);

        $cookie = App::make('cookie');

        // 这就等于set-cookie操作
        $cookie->queue($this->loginKey, $json, $this->expireTime);

        return true;
    }


    /**
     * 检查
     * @param $token
     * @return array
     */
    public function checkUserLogin2($token)
    {
        $userinfo = $this->loginUtil->getUserinfoByToken($token);

        return $userinfo;
    }


    /**
     * 利用cookie session原理的登录方式
     * @param Request $request
     * @return array|mixed
     */
    public function checkUserLogin(Request $request)
    {
        $cookie = $request->cookie();
//        $request = App::make('request');

        $userInfo = [];
        $cookieJson = $cookie->queued($this->loginKey);
        if ($cookieJson) {
            if ($cookieJson instanceof Cookie) {
                $cookieJson = $cookieJson->getValue();
                $userInfo = json_decode($cookieJson, true);
            }
        } else {
            $cookieJson = $request->cookie($this->loginKey);
            if ($cookieJson) {
                $userInfo = json_decode($cookieJson, true);
            }
        }

        $request->offsetSet('user_info', $userInfo);
        return $userInfo;
    }


    public function clearLogin($user)
    {
        $session = App::make('session');

        // 这个token怎么拿
        $token = '';

        $session->remove($token);

//        $cookie = App::make('cookie');
//        $cookie->queue($this->loginKey, $json, 10080);

        return true;
    }
}
