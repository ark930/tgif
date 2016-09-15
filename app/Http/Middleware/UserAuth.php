<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class UserAuth
{
    /**
     * 判断用户是否登录, 如果用户未登录, 则跳转到主界面
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (! Session::has('user')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
