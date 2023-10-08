<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Lib\Common\Util\RabbitmqService;

/**
 * 将blog数据推送到消息队列中，再同步到es
 * Class BlogSyncEsQueue
 * @package App\Jobs
 */
class BlogSyncEsQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $config;

    /**
     * Create a new job instance.
     * Queue constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->config = [
            'queue' => env('RABBITMQ_ES_QUEUE', 'blog_sync_es'),
            'exchange' => env('RABBITMQ_ES_EXCHANGE_NAME', 'blog_sync_es_exchange'),
            'routing_key' => env('RABBITMQ_ES_ROUTING_KEY', 'blog_sync_es_routingkey'),
        ];
    }


    /**
     * 推送消息
     * @param $data
     * @throws \Exception
     */
    public function pushData($data)
    {
        // 生产者塞入消息
        RabbitmqService::push($this->config['queue'], $this->config['exchange'], $this->config['routing_key'], $data);
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

    }
}
