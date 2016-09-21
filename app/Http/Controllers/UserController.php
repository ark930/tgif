<?php

namespace App\Http\Controllers;

use Storage;
use App\Contracts\SMSServiceContract;
use App\Exceptions\BadRequestException;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends BaseController
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

        return response('');
    }

    /**
     * 编辑本用户头像
     * 
     * @param Request $request
     * @return mixed
     * @throws BadRequestException
     */
    public function editUserAvatar(Request $request)
    {
        $user = $this->user();

        $old_avatar_url = $user['avatar_url'];

        if(!$request->hasFile('avatar')) {
            throw new BadRequestException('上传文件为空', 400);
        }

        $file = $request->file('avatar');
        if(!$file->isValid()) {
            throw new BadRequestException('文件上传出错', 400);
        }

        $newFileName = sha1(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
        $savePath = 'avatar/'.$newFileName;

        $bytes = Storage::put(
            $savePath,
            file_get_contents($file->getRealPath())
        );

        if(!Storage::exists($savePath)) {
            throw new BadRequestException('保存文件失败', 400);
        }

        $user['avatar_url'] = $savePath;
        $user->save();

        // 删除老文件
        Storage::delete($old_avatar_url);

        return response(Storage::get($savePath))
            ->header('Content-Type', Storage::mimeType($savePath));
    }

    /**
     * 获取用户头像
     *
     * @param Request $request
     * @return mixed
     * @throws BadRequestException
     */
    public function getUserAvatar(Request $request)
    {
        $user = $this->user();

        $avatar_url = $user['avatar_url'];

        if(empty($avatar_url) || !Storage::exists($avatar_url)) {
            throw new BadRequestException('用户头像不存在', 400);
        }

        return response(Storage::get($avatar_url))
            ->header('Content-Type', Storage::mimeType($avatar_url));
    }

    /**
     * 通过文件名获取头像
     *
     * @param Request $request
     * @param $avatar_name
     * @return mixed
     * @throws BadRequestException
     */
    public function getAvatarByName(Request $request, $avatar_name)
    {
        $avatar_url = 'avatar/' . $avatar_name;
        if(empty($avatar_url) || !Storage::exists($avatar_url)) {
            throw new BadRequestException('用户头像不存在', 400);
        }

        return response(Storage::get($avatar_url))
            ->header('Content-Type', Storage::mimeType($avatar_url));
    }

    /**
     * 查找用户
     *
     * @param Request $request
     * @return mixed
     */
    public function findUsers(Request $request)
    {
        $name = $request->input('name');

        $this->validateParams(compact('name'), [
            'name' => 'required',
        ]);

        $users = User::where('tel', 'like', "%$name%")
            ->orWhere('display_name', 'like', "%$name%")
            ->where('searchable', true)
            ->get();

        $res = [];
        foreach ($users as $user) {
            $res[] = [
                'id' => $user['id'],
                'username' => $user['user_name'],
                'display_name' => $user['display_name'] ?: $user['tel'],
                'avatar_url' => $user['avatar_url'],
                'type' => $user['type'],
            ];
        }

        return $res;
    }

    /**
     * 删除用户
     *
     * 用户手动删除, 删除前需要获取验证码, 解绑设备和手机
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws BadRequestException
     */
    public function delete(Request $request)
    {
        $user = $this->user();

        $this->validateParams($request->all(), [
            'verify_code' => 'required',
        ]);

        $verify_code = $request->input('verify_code');

        if(strtotime($user['verify_code_expire_at']) <= time()) {
            throw new BadRequestException('验证码失效, 请重新获取', 400);
        }
        
        if($user['verify_code'] != $verify_code) {
            throw new BadRequestException('验证码不正确', 400);
        }

        $devices = $user->devices;
        foreach ($devices as $device) {
            $device->delete();
        }

        $user->delete();

        return response('', 204);
    }
}
