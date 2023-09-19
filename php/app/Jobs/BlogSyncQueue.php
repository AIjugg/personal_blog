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
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->config = [
            'queue' => env('RABBITMQ_BLOG_QUEUE', 'blog_insert_edit'),
            'exchange' => env('RABBITMQ_BLOG_EXCHANGE_NAME', 'update_blog_exchange'),
            'routing_key' => env('RABBITMQ_BLOG_ROUTING_KEY', 'blog_edit_routingkey'),
        ];

        // 生产者塞入消息
        RabbitmqService::push($this->config['queue'], $this->config['exchange'], $this->config['routing_key'], $data);
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle()
    {
        RabbitmqService::pop($this->config['queue'], function ($message) {
            print_r($message);
        });
        exit;
    }
}
