<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class User
{
    public function getUser($id)
    {
        $user = DB::table('user')->find($id)->get()->toArray();
    }
}
