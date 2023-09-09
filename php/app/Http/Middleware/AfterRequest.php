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

        $messageArr = [
            "[using_time:{$useTime}s]",
            "[path:{$path}]",
            "[input:{$inputJson}]"
        ];
        $userInfo = $request->offsetGet('user_info');
        if (!empty($userInfo)) {
            $messageArr[] = "[uid:{$userInfo['id']}]";
        }

        // 在response中加入log_id
        if ($response instanceof JsonResponse) {
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
            $responseContent = $response->getContent();
            $jsonData = json_decode($responseContent, true);
            $jsonData['log_id'] = LOG_ID;
            $response = (new JsonResponse())->setData($jsonData);

            $messageArr[] = "[response:{$responseContent}]";
        }


        $message = implode(' ', $messageArr);

        $content = $response->getData();
        // 日志记录
        if ($content->code > 0) {
            Log::warning($message);
        } else {
            Log::info($message);
        }
        return $response;
    }
}
