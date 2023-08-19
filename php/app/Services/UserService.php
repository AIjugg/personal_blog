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
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $loginUtil;

    public function __construct()
    {
        $this->loginUtil = new Login();
    }


    /**
     * 获取用户实例
     * @param $username
     * @param int $state
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

        // 存储session登录token，cookie会自动返回给客户端
        $res = $this->storageLogin($user);

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


    public function storageLogin($user)
    {
        $session = App::make('session');

        $token = GenerateRandom::generateRandom();
        $json = json_encode($user, JSON_UNESCAPED_UNICODE);

        $session->put($token, $json);
        $session->migrate(true);

        $cookie = App::make('cookie');
        $cookie->queue('rem', $json, 10080);

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


    public function checkUserLogin()
    {
        $cookie = App::make('cookie');
        $request = App::make('request');

        $remInfo = [];
        $rem = $cookie->queued('rem');
        if ($rem) {
            if ($rem instanceof Cookie) {
                $rem = $rem->getValue();
                $remInfo = json_decode($rem, true);
            }
        } else {
            $rem = $request->cookie('rem');
            if ($rem) {
                $remInfo = json_decode($rem, true);
            }
        }

        $request->offsetSet('user_info', $remInfo);
        return $remInfo;


        $session = App::make('session');
        $data = $session->get('rem');
        if ($data) {
            return json_decode($data, true);
        }
        return false;
    }
}
