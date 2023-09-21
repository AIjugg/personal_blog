<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/home', 'IndexController@index')->name('home');

Route::group(['middleware' => ['after_request']], function () {
    Route::group(['prefix' => 'blog'], function () {
        Route::get('/blog-type-list', 'BlogController@listBlogType');
        Route::get('/blog-list', 'BlogController@getBlogList');
        Route::get('/get-blog-detail', 'BlogController@getBlogDetail');

    });


    Route::group(['middleware' => ['check_login']], function () {
        Route::group(['prefix' => 'blog'], function () {
            Route::post('/edit-blog-draft', 'BlogController@editBlogDraft');
            Route::post('/del-blog-draft', 'BlogController@delBlogDraft');
            Route::get('/list-blog-draft', 'BlogController@listBlogDraft');
            Route::get('/detail-blog-draft', 'BlogController@getDraftDetail');

            Route::post('/relation-blog-type', 'BlogController@relationBlogType');

            Route::post('/del-blog-type', 'BlogController@deleteBlogType');
            Route::post('/add-blog-type', 'BlogController@addBlogType');
            Route::post('/edit-blog-type', 'BlogController@editBlogType');

            Route::post('/edit-blog', 'BlogController@editBlog');  // 新增、编辑博客
            Route::get('/manager-blog-detail', 'BlogController@managerBlogDetail');  // 作者查看自己的博客
            Route::get('/manager-blog-list', 'BlogController@managerBlogList');  // 作者查看自己的博客

            Route::post('/sync-blog', 'BlogController@syncBlog');  // 将本地博客数据传到消息队列中
        });

        Route::group(['prefix' => 'user'], function () {
            Route::post('/logout', 'UserController@logoutByAccount');
            Route::get('/get-userinfo', 'UserController@getUserInfo');
            Route::post('/edit-userinfo', 'UserController@editUserInfo');


            Route::post('/logout-token', 'UserController@logoutByAccountToken');
            Route::post('/reset-password', 'UserController@resetPassword');
        });


        Route::group(['prefix' => 'index'], function () {
            Route::post('/upload-img-base64', 'IndexController@uploadImgBase64');
            Route::post('/upload-img', 'IndexController@uploadImage');
        });
    });

    Route::group(['prefix' => 'user'], function () {
        Route::post('/login', 'UserController@loginByAccount');
        Route::post('/register', 'UserController@registerByAccount');

        Route::post('/login-token', 'UserController@loginByAccountToken');


    });
});


