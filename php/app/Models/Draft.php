<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-10
 * Time: 17:50
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Draft extends BaseModel
{
    protected $table = 'blog_draft';

    /**
     * 获取博客草稿列表
     * @param $condition
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDraftQuery($condition)
    {
        $draftQuery = DB::table($this->table, 'd')
            ->where('d.is_deleted', 0);

        if (!empty($condition['uid'])) {
            $draftQuery->where('d.uid', $condition['uid']);
        }

        return $draftQuery;
    }


    public function getDraft($condition, $sortArr, $pageSet)
    {
        $blogQuery = $this->getDraftQuery($condition)->select(['d.draft_id','b.title','d.created_at','d.updated_at']);

        if (!empty($sortArr['sort_field']) && !empty($sortArr['sort_direction'])) {
            $blogQuery->orderBy('d.' . $sortArr['sort_field'], $sortArr['sort_direction']);
        } else {
            $blogQuery->orderBy('d.created_at', 'desc');
        }

        if (isset($pageSet['offset']) && !empty($pageSet['limit'])) {
            $blogQuery->offset($pageSet['offset'])
                ->limit($pageSet['limit']);
        }

        $result = $blogQuery->get()->toArray();
        return $result;
    }


    /**
     * 统计博客草稿数量
     * @param $condition
     * @return integer
     */
    public function countDraft($condition)
    {
        $blogQuery = $this->getDraftQuery($condition);

        $result = $blogQuery->count();

        return $result;
    }


    /**
     * 新增博客草稿
     * @param $data
     * @return bool
     */
    public function addDraft($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());
        $res = DB::table($this->table)->insert($data);

        return $res;
    }


    /**
     * 新增博客草稿
     * @param $data
     * @return int
     */
    public function addDraftGetId($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());

        $blogId = DB::table($this->table)->insertGetId($data);

        return $blogId;
    }


    /**
     * 编辑博客草稿
     * @param $condition
     * @param $data
     * @return int
     */
    public function editBlogDraft($condition, $data)
    {
        $draftQuery = DB::table($this->table);

        if (!empty($condition['draft_id'])) {
            $draftQuery->where('draft_id', $condition['draft_id']);
        }
        if (!empty($condition['uid'])) {
            $draftQuery->where('uid', $condition['uid']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $res = $draftQuery->update($data);

        return $res;
    }


    /**
     * 删除草稿
     * @param $condition
     * @return int
     */
    public function deleteDraft($condition)
    {
        $draftQuery = DB::table($this->table);

        if (!empty($condition['draft_id'])) {
            $draftQuery->where('draft_id', $condition['draft_id']);
        }
        if (!empty($condition['uid'])) {
            $draftQuery->where('uid', $condition['uid']);
        }

        $data = [
            'is_deleted' => 1,
            'updated_at' => date('Y-m-d H:i:s', time()),
        ];
        $res = $draftQuery->update($data);

        return $res;
    }


    /**
     * 获取草稿详情
     * @param array $condition
     * @return array
     */
    public function getDraftDetail($condition)
    {
        $query = DB::table($this->table)->select(['draft_id','title','uid','description','draft','top','state','image','created_at','updated_at']);

        if (!empty($condition['draft_id'])) {
            $query->where('draft_id', $condition['draft_id']);
        }
        if (!empty($condition['uid'])) {
            $query->where('uid', $condition['uid']);
        }

        $draft = $query->where('is_deleted', 0)
            ->get()->first();

        return (array)$draft;
    }
}
