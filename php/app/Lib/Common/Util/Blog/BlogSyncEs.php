<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-10-07
 * Time: 19:01
 */

namespace App\Lib\Common\Util\Blog;

use App\Jobs\BlogSyncEsQueue;
use xingwenge\canal_php\CanalClient;
use xingwenge\canal_php\CanalConnectorFactory;
use Com\Alibaba\Otter\Canal\Protocol\Column;
use Com\Alibaba\Otter\Canal\Protocol\Entry;
use Com\Alibaba\Otter\Canal\Protocol\EntryType;
use Com\Alibaba\Otter\Canal\Protocol\EventType;
use Com\Alibaba\Otter\Canal\Protocol\RowChange;
use Com\Alibaba\Otter\Canal\Protocol\RowData;
use App\Models\{Blog,BlogContent};


class BlogSyncEs
{

    protected $canalClient;

    protected $schema;

    protected $blog_table;
    protected $blog_content_table;

    const BLOG_UPDATE_WATCH_COLUMNS = ['title', 'description', 'is_deleted', 'state'];
    const BLOG_INSERT_WATCH_COLUMNS = ['blog_id', 'uid', 'title', 'description', 'is_deleted', 'state', 'create_time', 'updated_time'];
    const BLOG_CONTENT_WATCH_COLUMNS = ['content'];

    /**
     * BlogSyncEs constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        try {
            $this->schema = env('MYSQL_SYNC_ES_SCHEMA', 'changye_blog');
            $this->blog_table = (new Blog)->getTable();
            $this->blog_content_table = (new BlogContent)->getTable();

        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * 通过canal同步mysql数据到es
     * @throws \Exception
     */
    public function syncDataToMq()
    {
        try {
            $this->canalClient = CanalConnectorFactory::createClient(CanalClient::TYPE_SOCKET_CLUE);
            $this->canalClient->connect(config('database.canal.host'), config('database.canal.port'));
            $this->canalClient->checkValid();
            $this->canalClient->subscribe("1001", "example", ".*\\..*");

            while (true) {
                $msg = $this->canalClient->get(100);
                if ($entries = $msg->getEntries()) {
                    foreach ($entries as $entry) {
                        $this->parseEntry($entry);
                    }
                }
                sleep(1);
            }
            $this->canalClient->disConnect();
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @param Entry $entry
     * @throws \Exception
     */
    public function parseEntry($entry)
    {
        switch ($entry->getEntryType()) {
            case EntryType::TRANSACTIONBEGIN:
            case EntryType::TRANSACTIONEND:
                return;
                break;
        }

        $rowChange = new RowChange();
        $rowChange->mergeFromString($entry->getStoreValue());
        $evenType = $rowChange->getEventType();
        $header = $entry->getHeader();

//        echo sprintf("================> binlog[%s : %d],name[%s,%s], eventType: %s", $header->getLogfileName(), $header->getLogfileOffset(), $header->getSchemaName(), $header->getTableName(), $header->getEventType()), PHP_EOL;
//        echo $sql = $rowChange->getSql(), PHP_EOL;

        $mq = new BlogSyncEsQueue();

        // 同步这两张表的数据到消息队列，再消费消息队列将数据同步到es
        // 同步博客表
        if ($header->getSchemaName() == $this->schema && $header->getTableName() == $this->blog_table) {

            foreach ($rowChange->getRowDatas() as $rowData) {
                $changeData = ['table' => $this->blog_table];

                switch ($evenType) {
                    case EventType::DELETE:
                        $changeData['event'] = 'delete';
                        // delete
                        $columns = $rowData->getBeforeColumns();
                        /** @var Column $column */
                        foreach ($columns as $column) {
                            if ($column->getName() == 'blog_id') {
                                $changeData['blog_id'] = $column->getValue();
                                break;
                            }
                        }
                        break;
                    case EventType::INSERT:
                        $changeData['event'] = 'insert';
                        // insert
                        $columns = $rowData->getAfterColumns();
                        /** @var Column $column */
                        foreach ($columns as $column) {
                            if ($column->getName() == 'blog_id') {
                                $changeData['blog_id'] = $column->getValue();
                            }
                            // 监听字段
                            if (in_array($column->getName(), self::BLOG_INSERT_WATCH_COLUMNS)) {
                                $changeData['column'][$column->getName()] = $column->getValue();
                            }
                        }
                        break;
                    default:
                        $changeData['event'] = 'update';
                        // update
                        $columns = $rowData->getAfterColumns();
                        /** @var Column $column */
                        foreach ($columns as $column) {
                            if ($column->getName() == 'blog_id') {
                                $changeData['blog_id'] = $column->getValue();
                            }
                            // 如果有监听字段更新
                            if ($column->getUpdated() && in_array($column->getName(), self::BLOG_UPDATE_WATCH_COLUMNS)) {
                                $changeData['column'][$column->getName()] = $column->getValue();
                            }
                        }
                        // 如果不是监听字段更新，则不需要推送到消息队列
                        if (empty($changeData['column'])) {
                            unset($changeData);
                        }

                        break;
                }

                // 将$changeData写入消息队列
                if (!empty($changeData)) {
                    $mq->pushData($changeData);
                }
                unset($changeData);
            }
        }

        // 同步博客内容表
        if ($header->getSchemaName() == $this->schema && $header->getTableName() == $this->blog_content_table) {
            foreach ($rowChange->getRowDatas() as $rowData) {
                $changeData = ['table' => $this->blog_content_table];

                switch ($evenType) {
                    case EventType::DELETE:
                        $changeData['event'] = 'delete';
                        // delete
                        $columns = $rowData->getBeforeColumns();
                        /** @var Column $column */
                        foreach ($columns as $column) {
                            if ($column->getName() == 'blog_id') {
                                $changeData['blog_id'] = $column->getValue();
                                break;
                            }
                        }
                        break;
                    case EventType::INSERT:
                        $changeData['event'] = 'insert';
                        // insert
                        $columns = $rowData->getAfterColumns();
                        /** @var Column $column */
                        foreach ($columns as $column) {
                            if ($column->getName() == 'blog_id') {
                                $changeData['blog_id'] = $column->getValue();
                            }
                            // 监听字段
                            if (in_array($column->getName(), self::BLOG_CONTENT_WATCH_COLUMNS)) {
                                $changeData['column'][$column->getName()] = $column->getValue();
                            }
                        }
                        break;
                    default:
                        $changeData['event'] = 'update';
                        // update
                        $columns = $rowData->getAfterColumns();
                        /** @var Column $column */
                        foreach ($columns as $column) {
                            if ($column->getName() == 'blog_id') {
                                $changeData['blog_id'] = $column->getValue();
                            }
                            // 如果有监听字段更新
                            if ($column->getUpdated() && in_array($column->getName(), self::BLOG_CONTENT_WATCH_COLUMNS)) {
                                $changeData['column'][$column->getName()] = $column->getValue();
                            }
                        }
                        // 如果不是监听字段更新，则不需要推送到消息队列
                        if (empty($changeData['column'])) {
                            unset($changeData);
                        }

                        break;
                }

                // 将$changeData写入消息队列
                if (!empty($changeData)) {
                    $mq->pushData($changeData);
                }
            }
            unset($changeData);
        }
    }


    /**
     * 由于我的binlog日志是后面开启的，于是就导致无法通过binlog日志获取所有的博客数据，因此需要一个初始化同步数据的操作
     * @throws \Exception
     */
    public function initBlogData()
    {
        try {
            $blogModel = new Blog();
            $blogContentModel = new BlogContent();
            $mq = new BlogSyncEsQueue();

            $total = $blogModel->countBlog([]);
            for ($i = 0; $i <= $total; $i=$i+10) {
                $blogList = $blogModel->getBlog([], [], ['limit' => 10, 'offset' => $i]);

                if (empty($blogList)) {
                    break;
                }
                foreach ($blogList as $v) {
                    $blogData = [
                        'table' => $this->blog_table,
                        'event' => 'insert',
                        'blog_id' => $v['blog_id'],
                        'column' => [
                            'blog_id' => $v['blog_id'],
                            'uid' => $v['uid'],
                            'title' => $v['title'],
                            'description' => $v['description'],
                            'state' => $v['state'],
                            'create_time' => $v['created_at'],
                            'updated_time' => $v['updated_at'],
                        ]
                    ];
                    // 写入mq
                    $mq->pushData($blogData);
                }
                $blogIds = array_column($blogList, 'blog_id');

                $blogContent = $blogContentModel->getContent(['blog_id' => $blogIds]);
                foreach ($blogContent as $v) {
                    $blogContentData = [
                        'table' => $this->blog_content_table,
                        'event' => 'insert',
                        'blog_id' => $v['blog_id'],
                        'column' => [
                            'blog_id' => $v['blog_id'],
                            'content' => $v['content'],
                        ]
                    ];
                    // 写入mq
                    $mq->pushData($blogContentData);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
