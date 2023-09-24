<?php

namespace App\Console\Commands;

use App\Lib\Common\Util\Statistic\DataStatistic;
use Illuminate\Console\Command;

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
        // 更新博客浏览量
        return DataStatistic::updateViews();
    }
}
