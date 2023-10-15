<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-10-06
 * Time: 11:48
 */

namespace App\Lib\Common\Util\Blog;

use App\Jobs\BlogSyncEsQueue;
use App\Lib\Common\Util\CommonException;
use App\Lib\Common\Util\ErrorCodes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class BlogSearch
{
    /**
     * @var \Elasticsearch\Client $es
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
                            "format" => "yyyy-MM-dd HH:mm:ss"
                        ],
                        "updated_time" => [
                            "type" => "date",
                            "format" => "yyyy-MM-dd HH:mm:ss"
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


    /**
     * es搜索
     * @param $word
     * @param int $state
     * @return array
     * @throws \Exception
     */
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
                ],
                "min_score" => 2.0,
//                //高亮
//                "highlight" => [
//                    "fields" => [
//                        "title" => [
//                            "pre_tags" => ["<strong>"],
//                            "post_tags" => ["</strong>"]
//                        ]
//                    ]
//                ]
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

        try {
            $response = $this->es->search($params);

            $data = $response['hits']['hits'];
            $blogIds = [];
            if (empty($data)) {
                return [];
            }
            foreach ($data as $v) {
                $blogIds[] = $v['_source']['blog_id'];
            }
        } catch (\Exception $e) {
            throw $e;
        }


        // sql搜索
//        $sql = "SELECT * FROM " . self::IndexName . " WHERE title like %{$word}% OR `description` like %{$word}% OR `content` like %{$word}%";

        return $blogIds;
    }


    /**
     * 消费mq，将数据同步到es
     * @throws \Exception
     */
    public function syncBlogDataToEs()
    {
        $mq = new BlogSyncEsQueue();
        $mq->popData(function ($message) {
            $messageArr = json_decode($message, true);

            try {
                $blogId = $messageArr['blog_id'];
                if (empty($blogId)) {
                    throw new CommonException(ErrorCodes::BLOG_ID_EMPTY, $message);
                }
                $params = [
                    'index' => self::IndexName,
                    'type' => '_doc',
                    'id' => $blogId
                ];

                // 判断文档是否已存在
                $exist = $this->es->exists($params);

                switch ($messageArr['event']) {
                    case 'insert':
                        $this->handlerInsert($messageArr['table'], $exist, $blogId, $messageArr['column']);
                        break;
                    case 'update':
                        $this->handlerUpdate($blogId, $messageArr['column']);
                        break;
                    case 'delete':
                        $this->handlerDelete($blogId);
                        break;
                }

                return true;
            } catch (\Exception $e) {
                $errorMsg = 'error_line:' . $e->getLine() . ' error_msg:' . $e->getMessage();
                Log::channel('changye_blog_console')->warning($errorMsg);
                // exit可以强制退出，不返回ack信号，避免了消息因为程序执行异常而丢失。
                //exit;
                return false;
            }
        });
    }


    /**
     * 处理插入的情况
     * @param $tableName
     * @param $exist
     * @param $id
     * @param $data
     * @return array|bool
     */
    private function handlerInsert($tableName, $exist, $id, $data)
    {
        // 并不存在该文档
        if (!$exist) {
            $msgData = [];
            if ($tableName == 'blog') {
                 $msgData = [
                    "blog_id" => $data['blog_id'],
                    "uid" => $data['uid'],
                    "create_time" => $data['create_time'],
                    "updated_time" => $data['updated_time'],
                    "title" => $data['title'],
                    "description" => $data['description'],
                    "content" => '',
                    "state" => $data['state'],
                ];
            } elseif ($tableName == 'blogContent') {
                 $msgData = [
                    "blog_id" => $data['blog_id'],
                    "uid" => 0,
                    "create_time" => '1970-01-01 00:00:00',
                    "updated_time" => '1970-01-01 00:00:00',
                    "title" => '',
                    "description" => '',
                    "content" => $data['content'],
                    "state" => 2,
                ];
            }

            if (empty($msgData)) {
                return false;
            }

            $param = [
                'index' => self::IndexName,
                'type' => '_doc',
                'id' => $id,
                'body' => $msgData
            ];
            // 创建文档
            $response = $this->es->index($param);
            return $response;
        } else {
            // 已存在该文档
            unset($data['blog_id']);
            $param = [
                'index' => self::IndexName,
                'type' => '_doc',
                'id' => $id,
                'body' => ['doc' => $data]
            ];
            // 更新文档
            $response = $this->es->update($param);
            return $response;
        }
    }


    private function handlerUpdate($id, $data)
    {
        $param = [
            'index' => self::IndexName,
            'type' => '_doc',
            'id' => $id,
        ];

        if (!empty($data['is_deleted'])) {
            // 这个其实是软删除
            $response = $this->es->delete($param);
        } else {
            $param['body'] = ['doc' => $data];
            // 更新文档
            $response = $this->es->update($param);
        }
        return $response;
    }


    private function handlerDelete($id)
    {
        $param = [
            'index' => self::IndexName,
            'type' => '_doc',
            'id' => $id,
        ];

        $response = $this->es->delete($param);
        return $response;
    }
}
