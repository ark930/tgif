<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Session;

class AdminAuth
{
    /**
     * 判断是否完有访问后台管理页面的权限, 如果没有 则跳转到 Login 界面
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Session::has('user')) {
            return response('没有访问该页面的权限', 401);
        }

        $user = User::find(Session::get('user')['id']);
        if(empty($user)) {
            return response('没有访问该页面的权限', 401);
        }

        if($user->isAdmin() == false) {
            return response('没有访问该页面的权限', 401);
        }

        return $next($request);
    }
}