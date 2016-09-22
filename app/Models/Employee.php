<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'real_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function createEmployee($ceo_id, $username, $real_name, $position)
    {
        $ceo = User::find($ceo_id);
        $company = $ceo->nowEmployee->company;

        $user = new User();
        $user['inviter_id'] = $ceo['id'];
        $user['real_name'] = $real_name;
        $user['tel'] = $username;
        $user['apply_status'] = 'applying';
        $user->save();

        $employee = new Employee();
        $employee['user_id'] = $user['id'];
        $employee['company_id'] = $company['id'];
        $employee['real_name'] = $real_name;
        $employee['role'] = 'employee';
        $employee['position'] = $position;
        $employee->save();

        $user->nowEmployee()->associate($employee);
        $user->save();

    }
}