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
    Route::post('/del-blog-type', 'BlogController@deleteBlogType');
    Route::get('/get-blog-detail', 'BlogController@getBlogDetail');

});

