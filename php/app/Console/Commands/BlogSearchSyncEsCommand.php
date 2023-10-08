<?php

namespace App\Console\Commands;

use App\Lib\Common\Util\Blog\BlogSyncEs;
use Illuminate\Console\Command;
use App\Lib\Common\Util\BlogSearch;
use Illuminate\Support\Facades\Log;

class BlogSearchSyncEsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog_sync_to_es {method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将博客标题、描述、内容同步到es中，用于搜索';

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
            $method = $this->argument('method');

            if ($method == 'create') {  // 创建日志索引
                (new BlogSearch())->createIndex();
            } elseif (($method == 'sync')) {  // 使用canal同步mysql数据到es
                (new BlogSyncEs())->syncData();
            }
            var_dump('over');
            exit;
        } catch (\Exception $e) {
            Log::channel('changye_blog_console')->warning($e->getMessage());
        }
    }
}
