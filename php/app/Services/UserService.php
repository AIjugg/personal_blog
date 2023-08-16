<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-09
 * Time: 12:54
 */

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUserByAccount($username)
    {
        $user = (new User())->getUserByCondition(['username' => $username]);

        return $user;
    }
}
