<?php

namespace App\Console\Commands;

use App\Lib\Common\Util\CommonException;
use App\Lib\Common\Util\ErrorCodes;
use App\Lib\Common\Util\RabbitmqService;
use App\Services\BlogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncBlogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog_sync_to_mysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '消费博客同步队列，将数据插入到mysql中';


    private $config;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = [
            'queue' => env('RABBITMQ_BLOG_QUEUE', 'blog_insert_edit'),
            'exchange' => env('RABBITMQ_BLOG_EXCHANGE_NAME', 'update_blog_exchange'),
            'routing_key' => env('RABBITMQ_BLOG_ROUTING_KEY', 'blog_edit_routingkey'),
        ];

        parent::__construct();
    }

    /**
     *
     * @throws \Exception
     */
    public function handle()
    {
        RabbitmqService::pop($this->config['queue'], function ($message) {
            // 读取消息，再插入数据库
            $messageArr = json_decode($message, true);

            try {
                $content = $messageArr['content'];

                // 本地环境
                if (getenv('APP_ENV') == 'local') {
                    $find = "http://changye.top";
                    $replace = "https://www.laoziaite.asia";
                } else {  // 线上环境
                    $replace = "http://changye.top";
                    $find = "https://www.laoziaite.asia";
                }

                $newContent = str_replace($find, $replace, $content);

                $data = [
                    'title' => $messageArr['title'],
                    'description' => $messageArr['description'],
                    'image' => $messageArr['image'] ?? '',
                    'state' => $messageArr['state'],
                    'top' => $messageArr['top'],
                    'content' => $newContent,
                    'created_at' => $messageArr['created_at'],
                    'updated_at' => $messageArr['updated_at'],
                ];
                if (isset($messageArr['type_ids'])) {
                    $data['type_ids'] = $messageArr['type_ids'];
                }

                $uid = $messageArr['uid'];

                $blogService = new BlogService();
                $res = $blogService->addBlog($uid, $data);
                if (!$res) {
                    throw new CommonException(ErrorCodes::BLOG_ADD_FAIL);
                }
                echo "success" . PHP_EOL;
            } catch (\Exception $e) {
                $errorMsg = 'error_line:' . $e->getLine() . ' error_msg:' . $e->getMessage();
                Log::warning($errorMsg);
                // exit可以强制退出，不返回ack信号，避免了消息因为程序执行异常而丢失。
                exit;
            }
        });
    }
}
