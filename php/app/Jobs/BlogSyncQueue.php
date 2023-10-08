<?php

namespace App\Jobs;

use App\Lib\Common\Util\RabbitmqService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class BlogSyncQueue implements ShouldQueue
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
            'queue' => env('RABBITMQ_BLOG_QUEUE', 'blog_insert_edit'),
            'exchange' => env('RABBITMQ_BLOG_EXCHANGE_NAME', 'update_blog_exchange'),
            'routing_key' => env('RABBITMQ_BLOG_ROUTING_KEY', 'blog_edit_routingkey'),
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
     * 消费消息
     * 回调函数 @param $callback
     * @throws \Exception
     */
    public function popData($callback)
    {
        RabbitmqService::pop($this->config['queue'], $callback);
    }


    /**
     * laravel queue 消费者
     * Execute the job.
     * @throws \Exception
     *
     * @see \App\Console\Commands\SyncBlogCommand
     */
    public function handle()
    {
        // 用laravel的queue使用rabbitmq问题太多，放弃了，直接写了个Command，详见\App\Console\Commands\SyncBlogCommand

    }
}
