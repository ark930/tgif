<?php

namespace App\Http\Controllers\Api;

use App\Contracts\SMSServiceContract;
use App\Exceptions\BadRequestException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 获取登录验证码
     *
     * @param Request $request
     * @param SMSServiceContract $SMS
     * @return \Illuminate\Http\JsonResponse
     * @throws BadRequestException
     */
    public function verifyCode(Request $request, SMSServiceContract $SMS)
    {
        $this->validate($request, [
            'username' => 'required|regex:/^1\d{10}$/',
        ], [
            'username.required' => '请填写手机号',
            'username.regex' => '请填写正确的手机号',
        ]);

        $username = $request->input('username');

        $user = User::where('tel', $username)->first();
        if(empty($user)) {
            $user = User::create(['tel' => $username]);
        }
        
        if($user->ifGetVerifyCodeTooFrequently()) {
            $seconds = $user->verifyCodeRetryAfterSeconds();
            throw new BadRequestException("请求失败, 请在 $seconds 秒后重新请求", 400);
        }

        $verify_code = $user->setVerifyCode();

        // 向手机发送验证码短信
        $message = "【TGIF 验证】您的验证码是$verify_code";
        $SMS->SendSMS($username, $message);

        return response()->json(['msg' => '验证码已发送至客户端, 请注意查收']);
    }
}
