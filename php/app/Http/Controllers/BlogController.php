<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-11
 * Time: 19:46
 */

namespace App\Http\Controllers;

use App\Jobs\BlogSyncQueue;
use App\Lib\Common\Constant\SystemEnum;
use App\Lib\Common\Util\BlogSearch;
use App\Lib\Common\Util\CommonException;
use App\Lib\Common\Util\ErrorCodes;
use App\Lib\Common\Util\Helper;
use App\Lib\Common\Util\Statistic\DataStatistic;
use App\Services\BlogService;
use App\Services\BlogTypeService;
use App\Services\DraftService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Lib\Common\Util\ApiResponse;
use Illuminate\Routing\Controller as BaseController;

class BlogController extends BaseController
{

    /**
     * 分类列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listBlogType(Request $request)
    {
        try {
            $typeService = new BlogTypeService();
            $list = $typeService->listBlogType();
            $total = $typeService->countBlogType();
            $result = ApiResponse::buildResponse(['list' => $list, 'total' => $total]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 删除博客分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws CommonException
     */
    public function deleteBlogType(Request $request)
    {
        $input = $request->only(['type_id', 'if_force']);

        // 验证参数
        $validate = Validator::make($input, [
            'type_id' => ['required', 'integer'],
        ]);

        if ($validate->fails()) {
            $errorMsg = $validate->errors()->first();

            throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
        }

        // 获取登录用户的信息
        $userInfo = $request->offsetGet('user_info');
        if (empty($userInfo)) {
            throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
        }

        $typeId = $input['type_id'];
        // 是否强制删除
        $ifForce = $input['if_force'] ?? false;

        try {
            $typeService = new BlogTypeService();

            $relations = $typeService->getRelationBlogType($typeId);

            if (!$ifForce && !empty($relations)) {
                throw new CommonException(ErrorCodes::BLOG_TYPE_RELATION_EXIST);
            }
            if ($ifForce && !empty($relations)) {
                $typeService->delRelationBlogType(0, $typeId);
            }

            $data = $typeService->deleteBlogType($typeId);
            $result = ApiResponse::buildResponse($data);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }



    /**
     * 新增博客分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws CommonException
     */
    public function addBlogType(Request $request)
    {
        $input = $request->only(['type_name']);

        // 验证参数
        $validate = Validator::make($input, [
            'type_name' => ['required', 'string'],
        ]);

        if ($validate->fails()) {
            $errorMsg = $validate->errors()->first();

            throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
        }

        // 获取登录用户的信息
        $userInfo = $request->offsetGet('user_info');
        if (empty($userInfo)) {
            throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
        }

        $typeName = $input['type_name'];

        try {
            $data = (new BlogTypeService())->addBlogType($typeName);
            $result = ApiResponse::buildResponse($data);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 编辑博客分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws CommonException
     */
    public function editBlogType(Request $request)
    {
        $input = $request->only(['type_id','type_name']);

        // 验证参数
        $validate = Validator::make($input, [
            'type_id' => ['required', 'integer'],
            'type_name' => ['required', 'string'],
        ]);

        if ($validate->fails()) {
            $errorMsg = $validate->errors()->first();

            throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
        }

        // 获取登录用户的信息
        $userInfo = $request->offsetGet('user_info');
        if (empty($userInfo)) {
            throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
        }

        $typeId = $input['type_id'];
        $typeName = $input['type_name'];

        try {
            $data = (new BlogTypeService())->editBlogType($typeId,$typeName);
            $result = ApiResponse::buildResponse($data);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }

    /**
     * 获取博客详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBlogDetail(Request $request)
    {
        $input = $request->only(['blog_id']);

        $blogId = $input['blog_id'];

        try {
            $data = (new BlogService())->getBlogDetail(['blog_id' => $blogId]);
            $authorInfo = (new UserService())->getOneUserByUid($data['uid']);
            $data['nickname'] = $authorInfo['nickname'] ?? '';
            $data['profile_photo'] = $authorInfo['profile_photo'] ?? '';

            // 加上博客的分类
            $blogType = (new BlogTypeService())->blogRelationType($blogId);
            $data['types'] = $blogType[$blogId] ?? [];

            $result = ApiResponse::buildResponse(['detail' => $data]);

            // 博客浏览量统计
            DataStatistic::statisticViews($blogId);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 编辑博客|新增博客
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editBlog(Request $request)
    {
        try {
            $input = $request->only(['blog_id','title','description','image','state','top','content','type_ids']);

            // 验证参数
            $validate = Validator::make($input, [
                'blog_id' => ['nullable', 'integer'],
                'image' => 'nullable|string',
                'title' => 'required|string|max:50',
                'description' => ['required','string'],
                'state' => ['required', Rule::in([1, 2])],
                'top' => ['required', Rule::in([1, 2])],
                'content' => ['required'],
                'type_ids' => 'nullable|array'
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();
                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            $data = [
                'title' => $input['title'],
                'description' => $input['description'],
                'image' => $input['image'] ?? '',
                'state' => $input['state'],
                'top' => $input['top'],
                'content' => $input['content'],
            ];
            if (isset($input['type_ids'])) {
                $data['type_ids'] = $input['type_ids'];
            }

            // 获取登录用户的信息
            $userInfo = $request->offsetGet('user_info');
            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }
            $uid = $userInfo['id'];

            $blogService = new BlogService();
            if (!empty($input['blog_id'])) {
                $data['blog_id'] = $input['blog_id'];
                $res = $blogService->editBlog($uid, $data);
            } else {
                $res = $blogService->addBlog($uid, $data);
            }

            $result = ApiResponse::buildResponse($res);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 获取博客列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBlogList(Request $request)
    {
        try {
            $input = $request->only(['word','type_id','page','pagesize','sort_filed','sort_direction']);

            // 验证参数
            $validate = Validator::make($input, [
                'word' => 'nullable|string',
                'type_id' => 'nullable',
                'sort_filed' => ['nullable', Rule::in(['created_at','updated_at', 'like', 'pageview'])],
                'sort_direction' => ['nullable', Rule::in(['asc','desc'])],
                'page' => 'nullable|integer',
                'pagesize' => 'nullable|integer',
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            $condition = ['state' => SystemEnum::BLOG_STATE_NORMAL];

            // 关键词搜索
            if (!empty($input['word'])) {
                // $condition['title'] = trim($input['word']);

                // 使用es搜索
                $blogIds = (new BlogSearch())->searchBlogFromEs(trim($input['word']));
                if (empty($blogIds)) {
                    return response()->json(ApiResponse::buildResponse(['list' => [], 'total' => 0]));
                } else {
                    $condition['blog_id'] = $blogIds;
                }
            }

            if (!empty($input['type_id'])) {
                $condition['type_id'] = $input['type_id'];
            }

            $sortArr = Helper::sortStandard($input);
            $pageSet = Helper::pageStandard($input);

            $blogService = new BlogService();
            $list = $blogService->listBlog($condition, $sortArr, $pageSet);
            $total = $blogService->countBlog($condition);

            // 加上博客作者的信息
            if (!empty($list)) {
                $authorIds = array_unique(array_column($list, 'uid'));
                $authors = (new UserService())->getUserByUid($authorIds);
                $authorsUidKey = array_column($authors, null, 'id');

                // 加上博客的分类
                $blogIds = array_column($list, 'blog_id');
                $blogTypes = (new BlogTypeService())->blogRelationType($blogIds);
            }
            foreach ($list as $k=>$v) {
                $list[$k]['nickname'] = $authorsUidKey[$v['uid']]['nickname'] ?? '无名';
                $list[$k]['profile_photo'] = $authorsUidKey[$v['uid']]['profile_photo'] ?? '';

                $list[$k]['types'] = $blogTypes[$v['blog_id']] ?? [];
            }

            $result = ApiResponse::buildResponse(['list' => $list, 'total' => $total]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 博客关联分类（多个），包含删除操作
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function relationBlogType(Request $request)
    {
        try {
            $input = $request->only(['blog_id','type_ids']);

            // 验证参数
            $validate = Validator::make($input, [
                'blog_id' => ['required', 'integer'],
                'type_ids' => ['required', 'array']
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            // 用户登录
            $userInfo = $request->offsetGet('user_info');
            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }

            // 博客是本人的才可以
            $blog = (new BlogService())->getBlog(['blog_id' => $input['blog_id'], 'uid' => $userInfo['id']]);
            if (empty($blog)) {
                throw new CommonException(ErrorCodes::BLOG_TYPE_RELATION_FAIL);
            }

            $res = (new BlogTypeService())->relationBlogType($input['blog_id'], $input['type_ids']);

            $result = ApiResponse::buildResponse($res);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }



    /**
     * 新增|编辑草稿
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editBlogDraft(Request $request)
    {
        try {
            $input = $request->only(['draft_id','title','description','image','state','top','draft']);

            // 验证参数
            $validate = Validator::make($input, [
                'draft_id' => ['nullable', 'integer'],
                'image' => 'nullable|string',
                'title' => 'required|string|max:50',
                'description' => ['required','string'],
                'top' => ['required', Rule::in([1, 2])],
                'state' => ['required', Rule::in([1, 2])],
                'draft' => ['required', 'string']
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }
            // 获取用户id
            $userInfo = $request->offsetGet('user_info');
            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }
            $uid = $userInfo['id'];

            $data = [
                'title' => $input['title'],
                'description' => $input['description'],
                'image' => $input['image'] ?? '',
                'state' => $input['state'],
                'top' => $input['top'],
                'draft' => $input['draft'],
            ];

            if (!empty($input['draft_id'])) {
                $res = (new DraftService())->editDraft(['uid' => $uid, 'draft_id' => $input['draft_id']], $data);
            } else {
                $data['uid'] = $uid;
                $res = (new DraftService())->addDraft($data);
            }



            $result = ApiResponse::buildResponse($res);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 删除草稿
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delBlogDraft(Request $request)
    {
        try {
            $input = $request->only(['draft_id']);

            // 验证参数
            $validate = Validator::make($input, [
                'draft_id' => ['required', 'integer'],
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            // 获取用户id
            $userInfo = $request->offsetGet('user_info');
            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }
            $uid = $userInfo['id'];

            $condition = ['uid' => $uid];
            if (!empty($input['draft_id'])) {
                $condition['draft_id'] = $input['draft_id'];
            }

            $res = (new DraftService())->deleteDraft($condition);

            $result = ApiResponse::buildResponse($res);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 查看用户的博客草稿
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listBlogDraft(Request $request)
    {
        try {
            $input = $request->only(['page','pagesize','sort_filed','sort_direction']);

            // 验证参数
            $validate = Validator::make($input, [
                'sort_filed' => ['nullable', Rule::in(['created_at','updated_at'])],
                'sort_direction' => ['nullable', Rule::in(['asc','desc'])],
                'page' => 'nullable|integer',
                'pagesize' => 'nullable|integer',
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }
            $sortArr = Helper::sortStandard($input);
            $pageSet = Helper::pageStandard($input);

            // 获取用户id
            $userInfo = $request->offsetGet('user_info');
            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }
            $uid = $userInfo['id'];

            $condition = ['uid' => $uid];

            $draftService = new DraftService();
            $list = $draftService->listDraft($condition,$sortArr,$pageSet);
            $total = $draftService->countDraft($condition);

            $result = ApiResponse::buildResponse(['list' => $list, 'total' => $total]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 获取草稿详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDraftDetail(Request $request)
    {
        try {
            $input = $request->only(['draft_id']);

            // 验证参数
            $validate = Validator::make($input, [
                'draft_id' => 'required|integer'
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            // 获取用户id
            $userInfo = $request->offsetGet('user_info');
            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }

            $condition = ['uid' => $userInfo['id'], 'draft_id' => $input['draft_id']];

            $draftService = new DraftService();
            $detail = $draftService->detailDraft($condition);

            $result = ApiResponse::buildResponse(['detail' => $detail]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 管理后台获取博客详情（作者才能编辑自己的）
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function managerBlogDetail(Request $request)
    {
        $input = $request->only(['blog_id']);

        $blogId = $input['blog_id'];

        try {
            $selectField = ['b.blog_id', 'b.uid', 'b.title','b.description','b.image', 'bc.content', 'b.created_at', 'b.state', 'b.top'];

            // 获取用户id
            $userInfo = $request->offsetGet('user_info');
            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }
            $uid = $userInfo['id'];

            $data = (new BlogService())->getBlogDetail(['blog_id' => $blogId, 'uid' => $uid], $selectField);
            $authorInfo = (new UserService())->getOneUserByUid($data['uid']);
            $data['nickname'] = $authorInfo['nickname'] ?? '';
            $data['profile_photo'] = $authorInfo['profile_photo'] ?? '';

            // 加上博客的分类
            $blogType = (new BlogTypeService())->blogRelationType($blogId);
            $data['types'] = $blogType[$blogId] ?? [];

            $result = ApiResponse::buildResponse(['detail' => $data]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 作者查看自己的博客列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function managerBlogList(Request $request)
    {
        try {
            $input = $request->only(['word','type_id','page','pagesize','sort_filed','sort_direction']);

            // 验证参数
            $validate = Validator::make($input, [
                'word' => 'nullable|string',
                'type_id' => 'nullable',
                'sort_filed' => ['nullable', Rule::in(['created_at','updated_at', 'like', 'pageview'])],
                'sort_direction' => ['nullable', Rule::in(['asc','desc'])],
                'page' => 'nullable|integer',
                'pagesize' => 'nullable|integer',
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            // 获取用户id
            $userInfo = $request->offsetGet('user_info');
            if (empty($userInfo)) {
                throw new CommonException(ErrorCodes::USER_NOT_LOGIN);
            }
            $uid = $userInfo['id'];

            $condition = ['uid' => $uid];
            if (!empty($input['word'])) {
                $condition['title'] = trim($input['word']);
            }
            if (!empty($input['type_id'])) {
                $condition['type_id'] = $input['type_id'];
            }

            $sortArr = Helper::sortStandard($input);
            $pageSet = Helper::pageStandard($input);

            $blogService = new BlogService();
            $list = $blogService->listBlog($condition, $sortArr, $pageSet);
            $total = $blogService->countBlog($condition);

            // 加上博客作者的信息
            $authorIds = array_unique(array_column($list, 'uid'));
            $authors = (new UserService())->getUserByUid($authorIds);
            $authorsUidKey = array_column($authors, null, 'id');

            // 加上博客的分类
            $blogIds = array_column($list, 'blog_id');
            $blogTypes = (new BlogTypeService())->blogRelationType($blogIds);

            foreach ($list as $k=>$v) {
                $list[$k]['nickname'] = $authorsUidKey[$v['uid']]['nickname'] ?? '无名';
                $list[$k]['profile_photo'] = $authorsUidKey[$v['uid']]['profile_photo'] ?? '';

                $list[$k]['types'] = isset($blogTypes[$v['blog_id']]) ? $blogTypes[$v['blog_id']] : [];
            }

            $result = ApiResponse::buildResponse(['list' => $list, 'total' => $total]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 将本地博客同步到云服务器（异步双写）
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncBlog(Request $request)
    {
        try {
            $input = $request->only(['blog_id']);

            // 验证参数
            $validate = Validator::make($input, [
                'blog_id' => ['required', 'integer'],
            ]);
            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();
                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            $selectField = ['b.uid', 'b.title', 'b.description', 'b.image', 'b.state', 'b.top','bc.content', 'b.created_at', 'b.updated_at'];
            $blogData = (new BlogService())->getBlogDetail(['blog_id' => $input['blog_id']], $selectField);

            $data = [
                'uid' => $blogData['uid'],
                'title' => $blogData['title'],
                'description' => $blogData['description'],
                'image' => $blogData['image'] ?? '',
                'state' => $blogData['state'],
                'top' => $blogData['top'],
                'content' => $blogData['content'],
                'created_at' => $blogData['created_at'],
                'updated_at' => $blogData['updated_at']
            ];

            $relations = (new BlogTypeService())->getRelationBlogType(0, $input['blog_id']);
            if (!empty($relations)) {
                $typeIds = array_column($relations, 'type_id');
                $data['type_ids'] = $typeIds;
            }

            new BlogSyncQueue($data);

            $result = ApiResponse::buildResponse([]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }
        return response()->json($result);
    }
}
