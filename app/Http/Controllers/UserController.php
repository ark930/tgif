<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Support\Facades\Session;
use Storage;
use App\Services\IMService;
use App\Contracts\SMSServiceContract;
use App\Exceptions\BadRequestException;
use App\Models\Device;
use App\Models\Follower;
use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;

use App\Http\Requests;


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
        
        $verify_code_refresh_time = strtotime($user['verify_code_refresh_at']);
        if(!empty($user['verify_code_refresh_at']) && $verify_code_refresh_time > time()) {
            $seconds = $verify_code_refresh_time - time();
            throw new BadRequestException("请求失败, 请在 $seconds 秒后重新请求", 400);
        }

        $verify_code = mt_rand(100000, 999999);
        $verify_code_refresh_at = date('Y-m-d H:i:s', strtotime("+1 minute"));
        $verify_code_expire_at = date('Y-m-d H:i:s', strtotime("+5 minute"));
        $verify_code_retry_times = 4;

        $user['verify_code'] = $verify_code;
        $user['verify_code_refresh_at'] = $verify_code_refresh_at;
        $user['verify_code_expire_at'] = $verify_code_expire_at;
        $user['verify_code_retry_times'] = $verify_code_retry_times;
        $user->save();

        // 向手机发送验证码短信
        $message = "【云片网】您的验证码是$verify_code";
        $SMS->SendSMS($username, $message);

        return response(['msg' => '验证码已发送至手机, 请注意查收'], 200);
    }

    /**
     * 登录
     *
     * @param Request $request
     * @return mixed
     * @throws BadRequestException
     */
    public function login(Request $request)
    {
        $this->validateParams($request->all(), [
            'tel' => 'required|exists:users,tel',
            'verify_code' => 'required',
            'ip' => 'required',
            'client' => 'required',
        ]);

        $username = $request->input('tel');
        $verify_code = $request->input('verify_code');
        $user = User::where('tel', $username)->first();

        if(empty($user)) {
            throw new BadRequestException('登录失败', 400);
        }

        if(strtotime($user['verify_code_expire_at']) <= time()) {
            throw new BadRequestException('验证码过期, 请重新获取', 400);
        }

        if($user['verify_code_retry_times'] <= 0) {
            throw new BadRequestException('验证码输入错误次数过多, 已失效, 请重新获取', 400);
        }

        if($user['verify_code'] != $verify_code) {
            // 验证码错误, 重试次数减一
            $verify_code_retry_times = $user['verify_code_retry_times'];
            $verify_code_retry_times--;
            $user['verify_code_retry_times'] = $verify_code_retry_times;
            $user->save();

            throw new BadRequestException('验证码错误', 400);
        }

        $ip = $request->input('ip');
        $client = $request->input('client');

        $device = $user->devices()
            ->where('ip', $ip)
            ->where('client', $client)
            ->first();

        $api_token_length = config('message.api_token_length');
        $api_token = str_random($api_token_length);
        if(empty($device)) {
            $device = new Device([
                'user_id' => $user['id'],
                'ip' => $ip,
                'client' => $client,
                'api_token' => $api_token,
            ]);
        } else {
            $device['api_token'] = $api_token;
        }
        $device->save();

        $now = date('Y-m-d H:i:s', time());
        if(empty($user['first_login_at'])) {
            $user['first_login_at'] = $now;
        }
        // 登录成功后, 验证码立即失效
        $user['verify_code_expire_at'] = null;
        $user['verify_code_refresh_at'] = null;
        $user['verify_code_retry_times'] = 0;
        $user['last_login_at'] = $now;
        $user->save();

        $user['api_token'] = $api_token;

        return $user;
    }

    /**
     * 获取本用户个人信息
     * 
     * @param Request $request
     * @return mixed
     */
    public function getUserProfile(Request $request)
    {
        $user_id = $this->user_id();

        $user = User::find($user_id);

        return $user;
    }

    /**
     * 编辑个人信息
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws BadRequestException
     */
    public function editUserProfile(Request $request)
    {
        $user = $this->user();

        $username = $request->input('username');
        $display_name = $request->input('display_name');
        $email = $request->input('email');
        $tel = $request->input('tel');

        if(!empty($username)) {
            $user['user_name'] = $username;
        }

        if(!empty($display_name)) {
            $user['display_name'] = $display_name;
        }

        if(!empty($email)) {
            $user['email'] = $email;
        }

        if(!empty($tel)) {
            $user['tel'] = $tel;
        }

        $display_name = $user->getDisplayName();
        if(empty($user['avatar_url'])) {
            require(dirname(__FILE__) . "/md/MaterialDesign.Avatars.class.php");

            $avatar_word = mb_substr($display_name, 0, 1);
            $avatar = new \Md\MDAvatars($avatar_word);
            $newFileName = sha1(time().rand(0,10000)).'.png';
            $savePath = 'avatar/'.$newFileName;
            $tmpPath = '/tmp/'.$newFileName;
            $avatar->Save($tmpPath);
            $avatar->Free();

            $bytes = Storage::put(
                $savePath,
                file_get_contents($tmpPath)
            );

            if(!Storage::exists($savePath)) {
                throw new BadRequestException('保存文件失败', 400);
            }

            unlink($tmpPath);
            $user['avatar_url'] = $savePath;
        }

        $user->save();

        return response('', 200);
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
