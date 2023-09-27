<?php

namespace App\Console\Commands;

use App\Lib\Log\LogEsIndex;
use Illuminate\Console\Command;


class SyncLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_log_to_es {method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将日志同步到es中';

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
     * 同步指定日期的日志
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $method = $this->argument('method');

        $logEs = new LogEsIndex();
        if ($method == 'create') {  // 创建日志索引
            $logEs->createIndex();
        } else {  // 同步日志数据
            // 直接利用FileBeat传送数据给了logStash，再通过logStash同步到了es中。
        }
        var_dump('over');
        exit;

    }
}
