<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-08-11
 * Time: 19:46
 */

namespace App\Http\Controllers;

use App\Services\BlogService;
use App\Services\BlogTypeService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function listBlogType(Request $request)
    {
        $result = (new BlogTypeService())->listBlogType();

        return self::buildResponse($result);
    }


    public function deleteBlogType(Request $request)
    {
        $input = $request->only(['type_id']);

        $typeId = $input['type_id'];

        $result = (new BlogTypeService())->deleteBlogType($typeId);

        if (!empty($result)) {
            return self::buildResponse([]);
        } else {
            return self::buildThrowableResponse(1003, '软删除失败');
        }
    }


    public function getBlogDetail(Request $request)
    {
        $input = $request->only(['blog_id']);

        $blogId = $input['blog_id'];

        $result = (new BlogService())->getBlogDetail($blogId);

        if (!empty($result)) {
            return self::buildResponse($result);
        } else {
            return self::buildThrowableResponse(1004, '找不到相应博客');
        }
    }
}
