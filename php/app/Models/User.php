<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class User
{
    protected $table = 'user';

    /**
     * 获取用户
     * @param $condition
     * @param $selectField
     * @return array
     */
    public function getUserByCondition($condition, $selectField = [])
    {
        $query = DB::table($this->table);

        if (!empty($condition['id'])) {
            $query->where('id', $condition['id']);
        }
        if (!empty($condition['username'])) {
            $query->where('username', $condition['username']);
        }
        if (!empty($condition['email'])) {
            $query->where('email', $condition['email']);
        }
        if (!empty($condition['mobile'])) {
            $query->where('mobile', $condition['mobile']);
        }

        if (!empty($selectField)) {
            $query->select($selectField);
        }

        $user = $query->first();

        return (array)$user;
    }
}
