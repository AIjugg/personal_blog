<?php

namespace App\Http\Controllers;

use App\Lib\Common\Constant\SystemEnum;
use App\Lib\Common\Util\ApiResponse;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Lib\Common\Util\CommonException;
use App\Lib\Common\Util\ErrorCodes;

class UserController extends BaseController
{
    /**
     * 账号密码登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

            $userService = new UserService();
            $uid = $userService->loginByAccount($input['username'], $input['password']);

            // 更新用户信息
            $updateData = [
                'ip' => $request->ip(),
                'last_login' => date('Y-m-d H:i:s', time())
            ];
            $userService->editUser($uid, $updateData);

            $userInfo = $userService->getUserByUid($uid, SystemEnum::USER_STATE_NORMAL, ['id','username','email','mobile','nickname','profile_photo','sex','birthday','signature','state']);

            $result = ApiResponse::buildResponse(['userinfo' => $userInfo]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }


    /**
     * 账号密码注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerByAccount(Request $request)
    {
        $input = $request->post();
        try {
            // 验证参数
            $validate = Validator::make($input, [
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
                'password2' => ['required', 'string']
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }
            if ($input['password'] != $input['password2']) {
                throw new CommonException(ErrorCodes::USER_PWD_REPEAT_WRONG);
            }
            //$username = trim($input['username']);
            $username = $input['username'];
            $pattern = '/^[A-Za-z_]\w+$/';
            if (!preg_match($pattern, $username)) {
                throw new CommonException(ErrorCodes::USERNAME_WRONG);
            }

            $user = (new UserService())->getUserByName($username);
            if (!empty($user)) {
                throw new CommonException(ErrorCodes::USER_EXIST);
            }

            $res = (new UserService())->registerByAccount($username, $input['password']);

            $result = ApiResponse::buildResponse($res);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }


    public function registerByEmail(Request $request)
    {

    }


    public function registerByMobile(Request $request)
    {

    }


    /**
     * 登出
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutByAccount(Request $request)
    {
        try {
            $res = (new UserService())->logoutByAccount();
            $result = ApiResponse::buildResponse($res);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }


    /**
     * 获取用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo(Request $request)
    {
        try {
            // 获取登录用户的信息
            $userInfo = $request->offsetGet('user_info');
            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }

            $userService = new UserService();

            $userInfo = $userService->getUserByUid($userInfo['id'], SystemEnum::USER_STATE_NORMAL, ['id','username','email','mobile','nickname','profile_photo','sex','birthday','signature','state']);

            $result = ApiResponse::buildResponse(['userinfo' => $userInfo]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }
}
