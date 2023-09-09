<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-09-09
 * Time: 13:07
 */

namespace App\Lib\Log;

use Illuminate\Log\Logger;


class CustomizeLogFormatter
{
    /**
     * 自定义日志记录器实例
     * @param Logger $logger
     */
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter());
        }
    }
}
