<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function buildResponse(array $result)
    {
        $successData = $result;

        if (!isset($result['code']) && !isset($result['msg'])) {
            $successData = [
                'msg' => $result['msg'] ?? 'success',
                'code' => $result['code'] ?? '0',
                'data' => $result['data'] ?? $result
            ];
        }

        if (getenv('APP_DEBUG')) {

        }

        return response()->json($successData);
    }


    public static function buildThrowableResponse($errorCode = 1001, $errorMsg = 'error',array $result = [])
    {
        $errorData = $result;

        if (!isset($result['code']) && !isset($result['msg'])) {
            $errorData = [
                'msg' => $result['msg'] ?? $errorMsg,
                'code' => $result['code'] ?? $errorCode,
                'data' => $result['data'] ?? $result
            ];
        }

        if (getenv('APP_DEBUG')) {

        }

        return response()->json($errorData);
    }


}
