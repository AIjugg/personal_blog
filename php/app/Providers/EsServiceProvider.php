<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-09-25
 * Time: 11:30
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder as ESClientBuilder;

class EsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 注册一个名为 es 的单例
        $this->app->singleton('es', function () {
            $hosts = [
                [
                    'host' => config('database.elasticsearch.host'),
                    'port' => config('database.elasticsearch.port'),
                    'scheme' => config('database.elasticsearch.scheme'),
                    'user' => config('database.elasticsearch.user'),
                    'pass' => config('database.elasticsearch.pass'),
                ]
            ];

            // 从配置文件读取 Elastic Search 服务器列表
            $builder = ESClientBuilder::create()->setHosts($hosts);
            // 如果是开发环境
            if (app()->environment() === 'local') {
                // 配置日志，Elastic Search 的请求和返回数据将打印到日志文件中，方便我们调试
                $builder->setLogger(app('log')->driver());
            }

            return $builder->build();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }



}
