<?php

namespace App\Console\Commands;

use App\Lib\Common\Util\Statistic\DataStatistic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateBlogViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_blog_view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '读取redis哈希，更新博客浏览量';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // 更新博客浏览量
            return DataStatistic::updateViews();
        } catch (\Exception $e) {
            $errorMsg = 'error_line:' . $e->getLine() . ' error_msg:' . $e->getMessage();
            Log::channel('changye_blog_console')->warning($errorMsg);
            // exit可以强制退出，不返回ack信号，避免了消息因为程序执行异常而丢失。
            exit;
        }
    }
}
