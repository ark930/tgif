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

Route::post('/', 'Page\UserController@freeTrial');


// 邀请页面
Route::get('invite/{user_id}', 'Page\UserController@invite')
    ->where('user_id', '[0-9]+');

// 登录页面
Route::match(['get', 'post'], 'login', 'Page\UserController@login');

// 登出
Route::get('logout', function () {
    Session::flush();
    Session::regenerate();
    return redirect('/login');
});

// 需要用户登录才能访问的接口
Route::group(['middleware' => 'user_auth'], function() {
    // 审核页面
    Route::get('apply', 'Page\UserController@applyGet');
    Route::post('apply', 'Page\UserController@applyPost');

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

// 后台管理页面
Route::group([
    'prefix' => 'admin',
    'middleware' => 'admin_auth',
], function() {
    Route::get('/', 'Page\AdminController@index');

    Route::get('apply', ['as' => 'admin_apply', 'uses' => 'Page\AdminController@apply']);
    Route::get('approve', ['as' => 'admin_approve', 'uses' => 'Page\AdminController@approve']);
    Route::get('reject', ['as' => 'admin_reject', 'uses' => 'Page\AdminController@reject']);

    Route::post('apply/{action}/{user_id}', 'Page\AdminController@doApply')
        ->where([
            'action' => '(approve|reject)',
            'user_id' => '[0-9]+'
        ]);

    Route::get('data', ['as' => 'admin_data', 'uses' => 'Page\AdminController@data']);

});
