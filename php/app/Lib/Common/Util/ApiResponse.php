<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-15
 * Time: 17:07
 */

namespace App\Lib\Common\Util;


class ApiResponse
{
    public static function buildResponse(array $data = [], $code = 0, $msg = 'success')
    {
        $result = [
            'data' => $data,
            'status' => [
                'msg' => $msg,
                'code' => $code
            ]
        ];

        if (getenv('APP_DEBUG')) {

        }

        return $result;
    }


    /**
     * 处理错误
     * @param \Throwable $e
     * @param array $data
     * @return array
     */
    public static function buildThrowableResponse(\Throwable $e, array $data = [])
    {
        $code = $e->getCode();
        $msg = $e->getMessage();

        if (empty($code)) {
            $code = 9999;
        }
        if (empty($msg)) {
            $msg = '系统错误';
        }

        // 日志记录

        return [
            'data' => $data,
            'status' => [
                'msg' => $msg,
                'code' => $code
            ]
        ];
    }
}
