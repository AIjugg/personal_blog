<?php

namespace App\Http\Controllers;

use App\Lib\Common\Util\ApiResponse;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Lib\Common\Util\CommonException;
use App\Lib\Common\Util\ErrorCodes;

class UserController extends BaseController
{
    public function loginByAccount(Request $request)
    {
        $input = $request->only(['username', 'password']);
        try {

            // 验证参数
            $validate = Validator::make($input, [
                'username' => ['required', 'string'],
                'password' => ['required', 'string']
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            $user = (new UserService())->getUserByAccount($input['username']);

            $result = ApiResponse::buildResponse($user);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }
}
