<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-09
 * Time: 14:26
 */

namespace App\Http\Controllers;

use App\Lib\Common\Util\UploadFile;
use App\Services\MottoService;
use Illuminate\Http\Request;
use App\Lib\Common\Util\ApiResponse;
use Illuminate\Routing\Controller as BaseController;
use App\Lib\Common\Util\CommonException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Lib\Common\Util\ErrorCodes;


class IndexController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $data = (new MottoService())->getLastMotto();

            $result = ApiResponse::buildResponse($data);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 上传图片
     * 图片格式为base64
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImgBase64(Request $request)
    {
        try {
            $input = $request->only(['image','type']);

            // 验证参数
            $validate = Validator::make($input, [
                'image' => 'required|string',
                'type' => ['required', Rule::in(['cover','blog', 'profilePhoto'])],
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            $res = (new UploadFile())->uploadImgBase64($input['image'], $input['type']);

            $result = ApiResponse::buildResponse($res);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 上传文件的方式上传图片
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        try {
            $input = $request->only(['file', 'type']);
            if (!$request->hasFile('file')) {
                throw new CommonException(ErrorCodes::IMAGE_UPLOAD_EMPTY);
            }

            $file = $request->file('file');
            // 验证参数
            $validate = Validator::make($input, [
                'file' => 'required|image|max:2048',
                'type' => ['required', Rule::in(['cover','blog', 'profilePhoto'])],
            ]);
            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            // 保存文件
            $dirname = 'images/' . $input['type'];
            $path = $file->store($dirname, ['disk' => 'my_storage']);

            $imgUrl = env('APP_URL') . '/' . $path;

            $res = ['path' => $imgUrl];

            $result = ApiResponse::buildResponse($res);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }
}
