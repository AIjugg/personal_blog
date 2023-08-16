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
use App\Lib\Common\Util\Redis\Login;
use App\Models\User;
use App\Lib\Common\Constant\SystemEnum;
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

        // 存储登录token
        $res = $this->loginUtil->setUserToken($user);

        if (!$res) {
            throw new CommonException(ErrorCodes::USER_TOKEN_STORAGE_WRONG);
        }

        return $user['id'];
    }


    // 存储用户登录token，能不能做成依赖倒置
    public function storageLogin($uid)
    {

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


    public function checkUserLogin()
    {

    }
}
