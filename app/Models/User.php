<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'real_name', 'email', 'tel', 'api_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'verify_code', 'verify_code_expire_at', 'verify_code_refresh_at', 'verify_code_retry_times', 'deleted_at',
    ];

    public function employees()
    {
        return $this->hasMany('App\Models\Employee');
//        user_id 为 employees 表属性, id 为 users 表属性
//        return $this->hasMany('App\Models\Employee', 'user_id', 'id');
    }

    /**
     * 用户当前所任职的 Employee 对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nowEmployee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }


    /**
     * 被邀请人
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitees()
    {
        return $this->hasMany('App\Models\User', 'inviter_id');
    }

    /**
     * 邀请人
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inviter()
    {
        return $this->belongsTo('App\Models\User', 'inviter_id');
    }

    /**
     * 获取用户审核信息
     *
     * @return array
     */
    public function getApplyInfo()
    {
        $name = null;
        $company_name = null;
        $company_count = null;
        $position = null;
        $question1 = null;

        $employee = $this->nowEmployee;
        if(!empty($employee)) {
            $name = $employee['real_name'];
            $position = $employee['position'];

            $company = $employee->company;
            if(!empty($company)) {
                $company_name = $company['company_name'];
                $company_count = $company['company_count'];
                $form = $company->nowForm;
                if(!empty($form)) {
                    $question = $form->questions()->first();
                    if(!empty($question)) {
                        $question1 = $question['question'];
                    }
                }
            }
        }

        return [
            'name' => $name,
            'position' => $position,
            'company_name' => $company_name,
            'company_count' => !empty($company_count)? $company_count : '',
            'question1' => $question1,
//            'rank' => $this->rank(),
            'invite_link' => $this->inviteLink(),
        ];
    }

    /**
     * 用户在审核队列所处的排名
     *
     * @return mixed
     */
    public function rank()
    {
        return User::where('created_at', '<=', $this->created_at)
            ->where('apply_status', 'applying')
            ->count() + 32;
    }

    /**
     * 设置验证码
     */
    public function setVerifyCode()
    {
        $verify_code = mt_rand(100000, 999999);
        $verify_code_refresh_at = date('Y-m-d H:i:s', strtotime("+2 minute"));
        $verify_code_expire_at = date('Y-m-d H:i:s', strtotime("+2 minute"));
        $verify_code_retry_times = 4;

        $this['verify_code'] = $verify_code;
        $this['verify_code_refresh_at'] = $verify_code_refresh_at;
        $this['verify_code_expire_at'] = $verify_code_expire_at;
        $this['verify_code_retry_times'] = $verify_code_retry_times;
        $this->save();

        return $verify_code;
    }

    /**
     * 检查是否获取验证码过于频繁
     *
     * @return bool
     */
    public function ifGetVerifyCodeTooFrequently()
    {
        if(!empty($this['verify_code_refresh_at']) && strtotime($this['verify_code_refresh_at']) > time()) {
            return true;
        }

        return false;
    }

    /**
     * 需要多少秒才能重新获取验证码
     *
     * @return false|int
     */
    public function verifyCodeRetryAfterSeconds()
    {
        $seconds = strtotime($this['verify_code_refresh_at']) - time();

        return $seconds;
    }

    /**
     * 判断用户输入的验证码是否正确
     *
     * @param $userInputVerifyCode string 用户输入的验证码
     * @return bool
     */
    public function ifVerifyCodeWrong($userInputVerifyCode)
    {
        if($this['verify_code'] != $userInputVerifyCode) {
            // 验证码错误, 重试次数减一
            $this['verify_code_retry_times'] -= 1;
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * 判断验证码是否失效
     *
     * @return bool
     */
    public function ifVerifyCodeExpired()
    {
        if(strtotime($this['verify_code_expire_at']) <= time()) {
            return true;
        }

        return false;
    }

    /**
     * 判断验证码手否重试了太多次
     *
     * @return bool
     */
    public function ifVerifyCodeRetryTimesExceed()
    {
        if($this['verify_code_retry_times'] <= 0) {
            return true;
        }

        return false;
    }

    /**
     * 登录成功后, 验证码立即失效
     */
    public function disableVerifyCode()
    {
        $this['verify_code_expire_at'] = null;
        $this['verify_code_refresh_at'] = null;
        $this['verify_code_retry_times'] = 0;
        $this->save();
    }

    /**
     * 判断用户是否是第一次登录, 如果是第一次登录, 则设置第一次登录时间
     */
    public function ifFirstLogin()
    {
        if(empty($this['first_login_at'])) {
            $now = date('Y-m-d H:i:s', time());
            $this['first_login_at'] = $now;
            $this->save();
        }
    }

    /**
     * 更新最后一次登录的时间
     */
    public function updateLastLogin()
    {
        $now = date('Y-m-d H:i:s', time());
        $this['last_login_at'] = $now;
        $this->save();
    }

    /**
     * 判断是否具有管理员权限
     *
     * @return bool
     */
    public function isAdmin()
    {
        if($this['is_admin'] == 1) {
            return true;
        }

        return false;
    }

    /**
     * 获取用户要求链接
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    private function inviteLink()
    {
        return url('/?from=' . $this['id']);
    }

}
