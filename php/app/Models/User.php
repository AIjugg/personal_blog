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
            $username = $condition['username'];
            $query->where(function ($query) use ($username) {
                $query->orWhere('username', $username)
                    ->orWhere('email', $username)
                    ->orWhere('mobile', $username);
            });
        }

        $query->where('is_deleted', 0);

        if (!empty($condition['state'])) {
            $query->where('state', $condition['state']);
        }

        if (!empty($selectField)) {
            $query->select($selectField);
        }

        $user = $query->first();

        return (array)$user;
    }


    /**
     * 新增用户
     * @param $data
     * @return int
     */
    public function addUser($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());

        $res = DB::table($this->table)->insertGetId($data);

        return $res;
    }


    /**
     * 编辑用户
     * @param $uid
     * @param $data
     * @return int
     */
    public function editUser($uid, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $res = DB::table($this->table)->where('id', $uid)
            ->update($data);

        return $res;
    }
}
