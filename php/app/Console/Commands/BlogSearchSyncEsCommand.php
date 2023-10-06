<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Common\Util\BlogSearch;

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
        $method = $this->argument('method');

        $blogEs = new BlogSearch();
        if ($method == 'create') {  // 创建日志索引
            $blogEs->createIndex();
        } else {  // 同步mysql数据到es
            // 直接利用FileBeat传送数据给了logStash，再通过logStash同步到了es中。
        }
        var_dump('over');
        exit;
    }
}
