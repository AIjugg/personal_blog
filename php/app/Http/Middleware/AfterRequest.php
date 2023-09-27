<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-09-08
 * Time: 13:50
 */

namespace App\Http\Middleware;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Closure;

class AfterRequest
{
    /**
     * 响应之后处理
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $startTime = LARAVEL_START;
        $endTime = microtime(true);
        $useTime = $endTime - $startTime;


        // 请求路径
        $path = $request->path();
        // 输入
        $input = $request->input();
        $inputJson = json_encode($input, JSON_UNESCAPED_UNICODE);

        $uid = 0;
        $userInfo = $request->offsetGet('user_info');
        if (!empty($userInfo)) {
            $uid = $userInfo['id'];
        }

        $messageArr = [
            "using_time:{$useTime}s",
            "path:/{$path}",
            "uid:{$uid}",
            "input:{$inputJson}"
        ];

        // 在response中加入log_id
        if ($response instanceof JsonResponse) {
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
            $responseContent = $response->getContent();
            $jsonData = json_decode($responseContent, true);
            unset($jsonData['error_info']);
            $jsonData['log_id'] = LOG_ID;
            $response = (new JsonResponse())->setData($jsonData);

            $messageArr[] = "response:{$responseContent}";
        }


        $message = implode(' ', $messageArr);

        $content = $response->getData();
        // 日志记录
        if ($content->code > 0) {
            Log::warning($message);
        } else {
            // 请求超时的话，就记录下日志
            if ($useTime > 10) {
                Log::notice($message);
            }
        }
        return $response;
    }
}
