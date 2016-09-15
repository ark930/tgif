<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//  首页
Route::get('/', function () {
    return view('home');
});

// 登录页面
Route::match(['get', 'post'], 'login', 'Page\UserController@login');

// 需要用户登录才能访问的接口
Route::group(['middleware' => 'user_auth'], function() {
    // 审核页面
    Route::match(['get', 'post'], 'apply', 'Page\UserController@apply');

    // 需要审核通过才能访问的接口
    Route::group(['middleware' => 'apply_auth'], function() {
        // 时间线
        Route::get('dashboard/timeline', [ 'as' => 'timeline', function() {
            return view('timeline');
        }]);

        // 人
        Route::get('dashboard/people', [ 'as' => 'people', function() {
            return view('people');
        }]);

        // 问题
        Route::get('dashboard/surveys', [ 'as' => 'surveys', function() {
            return view('surveys');
        }]);
    });
});
