<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-11
 * Time: 19:46
 */

namespace App\Http\Controllers;

use App\Lib\Common\Util\CommonException;
use App\Lib\Common\Util\ErrorCodes;
use App\Lib\Common\Util\Helper;
use App\Services\BlogService;
use App\Services\BlogTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Lib\Common\Util\ApiResponse;
use Illuminate\Routing\Controller as BaseController;

class BlogController extends BaseController
{
    public function listBlogType(Request $request)
    {
        $data = (new BlogTypeService())->listBlogType();

        $result = ApiResponse::buildResponse($data);

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
        $input = $request->only(['type_id']);

        // 验证参数
        $validate = Validator::make($input, [
            'type_id' => ['required', 'integer'],
        ]);

        if ($validate->fails()) {
            $errorMsg = $validate->errors()->first();

            throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
        }

        $typeId = $input['type_id'];

        try {
            $data = (new BlogTypeService())->deleteBlogType($typeId);
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
            $data = (new BlogService())->getBlogDetail($blogId);
            $result = ApiResponse::buildResponse($data);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 创建博客
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addBlog(Request $request)
    {
        try {
            $input = $request->only(['title','description','image','state','top','content_simple','content']);

            // 验证参数
            $validate = Validator::make($input, [
                'title' => ['required', 'string'],
                'description' => ['required','string'],
                'state' => ['required', Rule::in([1, 2])],
                'top' => ['required', Rule::in([1, 2])],
                'content_simple' => ['required', 'string'],
                'content' => ['required']
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            $data = [
                'title' => $input['title'],
                'description' => $input['description'],
                'image' => $input['image'],
                'state' => $input['state'],
                'top' => $input['top'],
                'content_simple' => $input['content_simple'],
                'content' => $input['content']
            ];

            // 获取登录用户的信息
            $uid = 1;

            $res = (new BlogService())->addBlog($uid, $data);

            $result = ApiResponse::buildResponse($res);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }


    /**
     * 编辑博客
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editBlog(Request $request)
    {
        try {
            $input = $request->only(['blog_id','title','description','image','state','top','content_simple','content']);

            // 验证参数
            $validate = Validator::make($input, [
                'blog_id' => ['required', 'integer'],
                'title' => ['required', 'string'],
                'description' => ['required','string'],
                'state' => ['required', Rule::in([1, 2])],
                'top' => ['required', Rule::in([1, 2])],
                'content_simple' => ['required', 'string']
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            $data = [
                'blog_id' => $input['blog_id'],
                'title' => $input['title'],
                'description' => $input['description'],
                'image' => $input['image'],
                'state' => $input['state'],
                'top' => $input['top'],
                'content_simple' => $input['content_simple'],
                'content' => $input['content']
            ];

            // 获取登录用户的信息
            $uid = 1;

            $res = (new BlogService())->editBlog($uid, $data);

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
            $input = $request->only(['word','page','pagesize','sort_filed','sort_direction']);

            // 验证参数
            $validate = Validator::make($input, [
                'word' => 'string',
                'state' => ['nullable', Rule::in([1, 2])],
                'sort_filed' => ['nullable', Rule::in(['created_at', 'like', 'pageviews'])],
                'page' => 'nullable|integer',
                'pagesize' => 'nullable|integer',
            ]);

            if ($validate->fails()) {
                $errorMsg = $validate->errors()->first();

                throw new CommonException(ErrorCodes::PARAM_ERROR, $errorMsg);
            }

            $condition = [];
            if (!empty($input['word'])) {
                $condition['title'] = trim($input['word']);
            }

            $sortArr = Helper::sortStandard($input);
            $pageSet = Helper::pageStandard($input);

            $blogService = new BlogService();
            $list = $blogService->listBlog($condition, $sortArr, $pageSet);
            $total = $blogService->countBlog($condition);

            $result = ApiResponse::buildResponse(['list' => $list, 'total' => $total]);
        } catch (\Exception $e) {
            $result = ApiResponse::buildThrowableResponse($e);
        }

        return response()->json($result);
    }
}
