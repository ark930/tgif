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
     * 获取用户要求链接
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    private function inviteLink()
    {
        return url('invite/' . $this['id']);
    }

}
