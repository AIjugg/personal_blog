<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-09-25
 * Time: 11:43
 */

namespace App\Lib\Log;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class LogEsIndex
{
    /**
     * @see \Elasticsearch\ClientBuilder
     * @var mixed
     */
    protected $es;

    public function __construct()
    {
        $this->es = App::make('es');
    }


    /**
     * 创建es日志索引
     * @return mixed
     */
    public function createIndex()
    {
        $logIndex = [
            // 索引名称
            "index" => "changye_log",
            "body" => [
                "mappings" => [
                    "properties" => [
                        "log_id" => [
                            "type" => "keyword",     // keyword 表示对这个字段搜索必须是全名称，否则搜索不到对应内容
                            "index" => true,      // 是否被索引，true就会被索引，会被查询到，false则不会查询到
                        ],
                        "timestamp" => [
                            "type" => "date",
                            "format" => "yyyy-MM-dd hh:mm:ss"
                        ],
                        "level_name" => [
                            "type" => "constant_keyword",  //
                            "value" => "info",   // level_name没有提供值的话，则该字段将根据value自动设置值
                            "index" => true,
                        ],
                        "using_time" => [
                            "type" => "keyword",
                            "index" => false,
                        ],
                        "path" => [
                            "type" => "text",
                        ],
                        "uid" => [
                            "type" => "integer"
                        ],
                        "content" => [
                            "type" => "keyword",
                            "index" => false,
                        ],
                    ]
                ]
            ]
        ];

        try {
            $res = $this->es->indices()->create($logIndex);
            return $res;
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            Log::channel("changye_blog_console")->error($errorMsg);
        }
    }
}
