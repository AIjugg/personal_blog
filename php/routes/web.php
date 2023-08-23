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


Route::group(['prefix' => 'blog'], function () {
    Route::get('/blog-type-list', 'BlogController@listBlogType');
    Route::get('/blog-list', 'BlogController@getBlogList');
    Route::get('/get-blog-detail', 'BlogController@getBlogDetail');


});

Route::group(['middleware' => ['check_login']], function () {
    Route::group(['prefix' => 'blog'], function () {
        Route::post('/add-blog-draft', 'BlogController@addBlogDraft');
        Route::post('/edit-blog-draft', 'BlogController@editBlogDraft');
        Route::post('/del-blog-draft', 'BlogController@delBlogDraft');
        Route::get('/list-blog-draft', 'BlogController@listBlogDraft');

        Route::post('/relation-blog-type', 'BlogController@relationBlogType');
        Route::post('/del-relation-blog-type', 'BlogController@delRelationBlogType');

        Route::post('/del-blog-type', 'BlogController@deleteBlogType');
        Route::post('/add-blog-type', 'BlogController@addBlogType');
        Route::post('/edit-blog-type', 'BlogController@editBlogType');

    });

    Route::group(['prefix' => 'user'], function () {
        Route::post('/logout', 'UserController@logoutByAccount');
        Route::get('/get-userinfo', 'UserController@getUserInfo');
    });

});

Route::group(['prefix' => 'user'], function () {
    Route::post('/login', 'UserController@loginByAccount');
    Route::post('/register', 'UserController@registerByAccount');

});
