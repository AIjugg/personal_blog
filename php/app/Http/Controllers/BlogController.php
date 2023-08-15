<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-11
 * Time: 19:46
 */

namespace App\Http\Controllers;

use App\Exceptions\CommonException;
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
     */
    public function deleteBlogType(Request $request)
    {
        $input = $request->only(['type_id']);

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

                throw new CommonException($errorMsg, 1001);
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

                throw new CommonException($errorMsg, 1001);
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
}
