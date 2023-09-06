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

            $userInfo = $userService->getOneUserByUid($uid, SystemEnum::USER_STATE_NORMAL, ['id','username','email','mobile','nickname','profile_photo','sex','birthday','signature','state']);

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
                'password' => ['required', 'string']
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            //$username = trim($input['username']);
            $username = $input['username'];
            $pattern = '/^[A-Za-z_]\w+$/';
            if (!preg_match($pattern, $username)) {
                throw new CommonException(ErrorCodes::USERNAME_WRONG);
            }

            $user = (new UserService())->getOneUserByName($username);
            if (!empty($user)) {
                throw new CommonException(ErrorCodes::USERNAME_EXIST);
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

            $userInfo = $userService->getOneUserByUid($userInfo['id'], SystemEnum::USER_STATE_NORMAL, ['id','username','email','mobile','nickname','profile_photo','sex','birthday','signature','state']);

            $result = ApiResponse::buildResponse(['userinfo' => $userInfo]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }


    /**
     * 账号密码登录返回token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginByAccountToken(Request $request)
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
            $res = $userService->loginByAccountToken($input['username'], $input['password']);

            // 更新用户信息
            $updateData = [
                'ip' => $request->ip(),
                'last_login' => date('Y-m-d H:i:s', time())
            ];
            $userService->editUser($res['uid'], $updateData);

            $userInfo = $userService->getOneUserByUid($res['uid'], SystemEnum::USER_STATE_NORMAL, ['id','username','email','mobile','nickname','profile_photo','sex','birthday','signature','state']);

            $result = ApiResponse::buildResponse(['userinfo' => $userInfo, 'access_token' => $res['access_token']]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }



    /**
     * 登出
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutByAccountToken(Request $request)
    {
        try {
            $accessTokenHeader = $request->header('Authorization');
            if (!empty($accessTokenHeader)) {
                $accessToken = substr($accessTokenHeader, 7);
            }
            $res = (new UserService())->logoutByAccountToken($accessToken ?? '');
            $result = ApiResponse::buildResponse($res);

        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }


    /**
     * 重置密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $input = $request->only(['ori_password', 'new_password']);
        try {
            // 验证参数
            $validate = Validator::make($input, [
                'ori_password' => ['required', 'string'],
                'new_password' => ['required', 'string']
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }
            if ($input['ori_password'] == $input['new_password']) {
                throw new CommonException(ErrorCodes::NEW_PWD_REPEAT);
            }

            // 获取用户id
            $userInfo = $request->offsetGet('user_info');
            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }
            $uid = $userInfo['id'];

            $userService = new UserService();
            $res = $userService->resetPassword($uid, $input['ori_password'], $input['new_password']);

            // 需要重新登录
            $accessTokenHeader = $request->header('Authorization');
            if (!empty($accessTokenHeader)) {
                $accessToken = substr($accessTokenHeader, 7);
            }
            $userService->logoutByAccountToken($accessToken ?? '');

            $result = ApiResponse::buildResponse($res);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }


    /**
     * 编辑用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws CommonException
     */
    public function editUserInfo(Request $request)
    {
        $input = $request->only(['nickname','profile_photo','sex','birthday','signature']);

        // 验证参数
        $validate = Validator::make($input, [
            'nickname' => ['required', 'string'],
            'profile_photo' => ['nullable', 'string'],
            'sex' => ['required', 'integer'],
            'birthday' => ['required', 'string'],
            'signature' => ['required', 'string'],
        ]);

        if ($validate->fails()) {
            $errorMsg = $validate->errors()->first();

            throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
        }

        // 获取登录用户的信息
        $userInfo = $request->offsetGet('user_info');
        if (empty($userInfo)) {
            throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
        }
        $uid = $userInfo['id'];

        $data = [
            'nickname' => $input['nickname'],
            'profile_photo' => $input['profile_photo'] ?? '',
            'sex' => $input['sex'],
            'birthday' => $input['birthday'],
            'signature' => $input['signature'],
        ];

        try {
            $data = (new UserService())->editUser($uid,$data);
            $result = ApiResponse::buildResponse($data);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }
}
