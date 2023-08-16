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
use App\Models\User;
use App\Lib\Common\Constant\SystemEnum;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * 获取用户实例
     * @param $username
     * @param int $state
     * @return array
     */
    public function getUserByName($username, $state = SystemEnum::USER_STATE_NORMAL)
    {
        $condition = ['username' => $username];
        if (!empty($state)) {
            $condition['state'] = $state;
        }

        $user = (new User())->getUserByCondition($condition);

        return $user;
    }


    public function loginByAccount($username, $password)
    {
        $user = $this->getUserByName($username);

        // 验证密码
        if (!$this->verifyPasswordBcrypt($password, $user['password'])) {
            throw new CommonException(ErrorCodes::USER_PWD_WRONG);
        }

        // 验证成功，更新用户信息
        $uid = $user['id'];
        $updateData = [

        ];

        // 记录用户登录信息

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

        return [];
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


    public function editUser($uid, $data)
    {

    }


    public function checkUserLogin()
    {

    }
}
