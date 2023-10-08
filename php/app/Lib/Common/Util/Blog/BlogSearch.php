<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-10-06
 * Time: 11:48
 */

namespace App\Lib\Common\Util;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class BlogSearch
{
    /**
     * @see \Elasticsearch\ClientBuilder
     * @var mixed
     */
    protected $es;

    const IndexName = "changye_blog";

    public function __construct()
    {
        $this->es = App::make('es');
    }


    /**
     * 创建es博客内容索引
     * @return mixed
     */
    public function createIndex()
    {
        $blogIndex = [
            // 索引名称
            "index" => self::IndexName,
            "body" => [
                "mappings" => [
                    "properties" => [
                        "blog_id" => [
                            "type" => "integer",
                        ],
                        "uid" => [
                            "type" => "integer"
                        ],
                        "create_time" => [
                            "type" => "date",
                            "format" => "yyyy-MM-dd hh:mm:ss"
                        ],
                        "updated_time" => [
                            "type" => "date",
                            "format" => "yyyy-MM-dd hh:mm:ss"
                        ],
                        "title" => [
                            "type" => "text",
                            "index" => true,
                            "analyzer" => "ik_smart",    // 选择分词器
                        ],
                        "description" => [
                            "type" => "text",
                            "index" => true,
                            "analyzer" => "ik_smart",    // 选择分词器
                        ],
                        "content" => [
                            "type" => "text",
                            "index" => true,
                            "analyzer" => "ik_smart",    // 选择分词器
                        ],
                        "state" => [
                            "type" => "integer",  // 状态码
                        ]
                    ]
                ]
            ]
        ];

        try {
            $res = $this->es->indices()->create($blogIndex);
            return $res;
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            Log::channel("changye_blog_console")->error($errorMsg);
        }
    }


    public function searchBlogFromEs($word, $state = 0)
    {
        // es格式的搜索
        $params = [
            'index' => self::IndexName,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            [
                                'match' => [
                                    'title' => $word
                                ]
                            ],
                            [
                                'match' => [
                                    'description' => $word
                                ]
                            ],
                            [
                                'match' => [
                                    'content' => $word
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if (!empty($state)) {
            $params['body']['query']['bool']['must'] = [
                [
                    "term" => [
                        "state" => $state
                    ]
                ]
            ];
        }

        $response = $this->es->search($params);

        $data = $response['hits']['hits'];
        $blogIds = [];
        if (empty($data)) {
            return [];
        }
        foreach ($data as $v) {
            $blogIds[] = $v['_source']['blog_id'];
        }


        // sql搜索
//        $sql = "SELECT * FROM " . self::IndexName . " WHERE title like %{$word}% OR `description` like %{$word}% OR `content` like %{$word}%";

        return $blogIds;
    }
}
