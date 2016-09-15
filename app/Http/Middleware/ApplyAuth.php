<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Session;

class ApplyAuth
{
    /**
     * 判断是否完成审核, 如果没有完成审核, 则跳转到 apply 界面
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Session::get('user');
        $user = User::find($user['id']);
        Session::put('user', $user);

        if (isset($user) && $user['apply_status'] != 'approve') {
            return redirect('apply');
        }

        return $next($request);
    }
}
