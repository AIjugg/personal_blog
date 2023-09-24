<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-09-22
 * Time: 17:09
 */

namespace App\Lib\Common\Util\Statistic;

use App\Lib\Common\Util\CommonException;
use App\Lib\Common\Util\ErrorCodes;
use App\Models\Blog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class DataStatistic
{
    const BLOG_VIEW_KEY = 'blog_view_statistic';


    /**
     * Redis方法 @see \Illuminate\Redis\Connections\PhpRedisConnection
     *
     * 博客浏览量加1
     * 使用hincrby方法，field原本不存在则创建后增加int值
     * @param $blogId
     * @throws CommonException
     */
    public static function statisticViews($blogId)
    {
        if (empty($blogId)) {
            throw new CommonException(ErrorCodes::BLOG_NOT_EXIST);
        }

        Redis::command('hincrby', [self::BLOG_VIEW_KEY, $blogId ,1]);
    }


    /**
     * 更新博客浏览量
     * @return bool
     */
    public static function updateViews()
    {
        // 获取所有博客浏览数据
        $viewData = Redis::command('hgetall', [self::BLOG_VIEW_KEY]);

        if (empty($viewData)) {
            return true;
        }

        $blog = new Blog();
        foreach ($viewData as $blogId => $viewNum) {
            if (empty($viewNum)) {
                Redis::command('hdel', [self::BLOG_VIEW_KEY, $blogId]);
                continue;
            }

            // 其实更新完之后再删除哈希表的数据，可能会出现哈希表中数据已经更新过的情况。不过数据浏览量的统计不需要如此精确
            // redis
            $res = $blog->updateBlogStatistic($blogId, 'pageviews', $viewNum);
            if ($res) {
                $decrNum = (-1) * $viewNum;
                Redis::command('hincrby', [self::BLOG_VIEW_KEY, $blogId ,$decrNum]);
            } else {
                Log::warning('update blog view error: '. $blogId);
            }
        }

        return true;
    }
}
