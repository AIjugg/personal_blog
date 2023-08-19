<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-12
 * Time: 20:21
 */

namespace App\Services;

use App\Lib\Common\Util\ErrorCodes;
use App\Models\Draft;
use App\Lib\Common\Util\CommonException;

class DraftService
{
    /**
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function addDraft($data)
    {
        $draftData = [
            'blog_id' => $data['blog_id'],
            'draft' => $data['draft']
        ];

        // 先插入博客获取博客Id
        $draftId = (new Draft())->addDraftGetId($draftData);
        if (empty($draftId)) {
            throw new CommonException(ErrorCodes::DRAFT_ADD_FAIL);
        }

        return ['draft_id' => $draftId];
    }


    /**
     * 编辑草稿
     * @param $condition
     * @param $data
     * @return array
     * @throws CommonException
     */
    public function editDraft($condition, $data)
    {
        if (empty($condition['draft_id'])) {
            throw new CommonException(ErrorCodes::PARAM_ERROR);
        }

        $res = (new Draft())->editBlogDraft($condition, ['draft' => $data['draft']]);

        if (empty($res)) {
            throw new CommonException(ErrorCodes::DRAFT_EDIT_FAIL);
        }

        return [];
    }


    /**
     * 删除草稿
     * @param $condition
     * @return array
     * @throws CommonException
     */
    public function deleteDraft($condition)
    {
        if (empty($condition['blog_id']) && empty($condition['draft_id'])) {
            throw new CommonException(ErrorCodes::PARAM_ERROR);
        }

        $result = (new Draft())->deleteDraft($condition);

        if (empty($result)) {
            throw new CommonException(ErrorCodes::DRAFT_DELETE_FAIL);
        } else {
            return [];
        }
    }


    /**
     * 草稿列表
     * @param $condition
     * @param array $sortArr
     * @param array $pageSet
     * @return array
     */
    public function listDraft($condition, $sortArr = [], $pageSet = [])
    {
        $result = (new Draft())->getDraft($condition, $sortArr, $pageSet);

        return $result;
    }


    public function countDraft($condition)
    {
        $total = (new Draft())->countDraft($condition);

        return $total;
    }
}
