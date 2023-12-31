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
use App\Lib\Common\Util\Login\LoginByToken;
use App\Models\User;
use App\Lib\Common\Constant\SystemEnum;
use Illuminate\Support\Facades\App;
use App\Lib\Common\Util\EncryptHelper;
use App\Lib\Common\Util\Login\LoginAccount;


class UserService
{

    public function __construct()
    {

    }


    /**
     * 获取一个用户实例
     * @param $username
     * @param int $state
     * @param array $selectField
     * @return array
     */
    public function getOneUserByName($username, $state = SystemEnum::USER_STATE_NORMAL, $selectField = [])
    {
        $condition = ['username' => $username];
        if (!empty($state)) {
            $condition['state'] = $state;
        }

        $user = (new User())->getOneUserByCondition($condition, $selectField);

        return $user;
    }


    /**
     * 获取用户实例
     * @param int|array $uid
     * @param int $state
     * @param array $selectField
     * @return array
     */
    public function getOneUserByUid($uid, $state = SystemEnum::USER_STATE_NORMAL, $selectField = [])
    {
        $condition = ['id' => $uid];
        if (!empty($state)) {
            $condition['state'] = $state;
        }

        $user = (new User())->getOneUserByCondition($condition, $selectField);

        foreach ($user as $k => $v) {
            if (empty($v)) {
                $user[$k] = '';
            }
        }

        return $user;
    }


    /**
     * 获取用户实例
     * @param int|array $uid
     * @param int $state
     * @param array $selectField
     * @return array
     */
    public function getUserByUid($uid, $state = SystemEnum::USER_STATE_NORMAL, $selectField = [])
    {
        $condition = ['id' => $uid];
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
        $userInfo = $this->getOneUserByName($username, SystemEnum::USER_STATE_NORMAL, ['id','username','email','mobile','password','state','last_login']);

        if (empty($userInfo)) {
            throw new CommonException(ErrorCodes::USER_NOT_EXIST);
        }

        // 验证密码
        if (!EncryptHelper::verifyPasswordBcrypt($password, $userInfo['password'])) {
            throw new CommonException(ErrorCodes::USER_PWD_WRONG);
        }
        unset($userInfo['password']);

        // session存储登录信息，cookie返回给客户端
        $session = App::make('session');
        $cookie = App::make('cookie');
        $res = (new LoginAccount())->storageLogin($userInfo, $session, $cookie);

        if (!$res) {
            throw new CommonException(ErrorCodes::USER_TOKEN_STORAGE_WRONG);
        }

        return $userInfo['id'];
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
        $encryption = EncryptHelper::encryptPasswordBcrypt($password);

        $userData = [
            'username' => $username,
            'password' => $encryption,
            'nickname' => $username
        ];

        $res = (new User())->addUser($userData);
        if (empty($res)) {
            throw new CommonException(ErrorCodes::ADD_USER_WRONG);
        }

        return ['uid' => $res];
    }


    /**
     * 登出
     * @return array
     */
    public function logoutByAccount()
    {
        $session = App::make('session');
        $cookie = App::make('cookie');
        $res = (new LoginAccount())->clearLogin($session, $cookie);

        return $res;
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


    /**
     * 用户账号密码登录，返回access_token
     * @param $username
     * @param $password
     * @return array
     * @throws \Exception
     */
    public function loginByAccountToken($username, $password)
    {
        try {
            $userInfo = $this->getOneUserByName($username, SystemEnum::USER_STATE_NORMAL, ['id', 'username', 'email', 'mobile', 'password', 'state', 'last_login']);

            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_EXIST);
            }

            // 验证密码
            if (!EncryptHelper::verifyPasswordBcrypt($password, $userInfo['password'])) {
                throw new CommonException(ErrorCodes::USER_PWD_WRONG);
            }
            unset($userInfo['password']);

            // 获取token
            $token = (new LoginByToken())->storageLogin($userInfo);

            return ['access_token' => $token, 'uid' => $userInfo['id']];
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * 登出
     * @param $token
     * @return array
     * @throws \Exception
     */
    public function logoutByAccountToken($token)
    {
        try {
            if (empty($token)) {
                throw new CommonException(ErrorCodes::TOKEN_NOT_EXIST);
            }
            (new LoginByToken())->clearLogin($token);

            return [];
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * 重置密码
     * @param $uid
     * @param $oriPwd
     * @param $newPwd
     * @return array
     * @throws \Exception
     */
    public function resetPassword($uid, $oriPwd, $newPwd)
    {
        try {
            $userInfo = $this->getOneUserByUid($uid, SystemEnum::USER_STATE_NORMAL, ['id', 'username', 'email', 'mobile', 'password', 'state', 'last_login']);

            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_EXIST);
            }

            // 验证密码
            if (!EncryptHelper::verifyPasswordBcrypt($oriPwd, $userInfo['password'])) {
                throw new CommonException(ErrorCodes::USER_PWD_WRONG);
            }

            $encryption = EncryptHelper::encryptPasswordBcrypt($newPwd);
            $updateUserInfo = [
                'password' => $encryption
            ];

            $this->editUser($uid, $updateUserInfo);

            return [];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
