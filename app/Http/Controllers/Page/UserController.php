<?php

namespace App\Http\Controllers\Page;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if($request->isMethod('post')) {
            $username = $request->input('username');
            $password = $request->input('password');

            if(empty($username)) {
                return redirect()->back()->with('error1', '(请填写手机号)')->withInput();
            }

            if(empty($password)) {
                return redirect()->back()->with('error2', '(请填写验证码)')->withInput();
            }

            $user = User::where('tel', $username)
                ->where('verify_code', $password)
                ->first();
            if(empty($user)) {
                return redirect()->back()->with('error3', '(用户名或验证码错误)')->withInput();
            }

            Session::put('user', $user);

            if($user['apply_status'] == 'approve') {
                return redirect()->route('people');
            } else if($user['apply_status'] == 'reject') {
                return '审核未通过';
            } else {
                return redirect('apply');
            }
        } else {
            return view('login');
        }
    }

    public function apply(Request $request)
    {
        $user = Session::get('user');

        if($request->isMethod('post')) {
            $name = $request->input('name');
            $company_name = $request->input('company');
            $position = $request->input('position');
            $question1 = $request->input('question1');
            $question2 = $request->input('question2');
            $question3 = $request->input('question3');

            if(empty($name)) {
                return redirect()->back()->with('error1', '(请填写名称)');
            }

            if(empty($company_name)) {
                return redirect()->back()->with('error2', '(请填写公司名)');
            }

            if(empty($position)) {
                return redirect()->back()->with('error3', '(请填写职位)');
            }

            $user['real_name'] = $name;
            $user->save();

            $employee = Employee::where('user_id', $user['id'])->first();
            if(empty($employee)) {
                $employee = new Employee();
                $employee['user_id'] = $user['id'];
                $employee['role'] = 'owner';
            }
            $employee['real_name'] = $name;
            $employee['position'] = $position;
            $employee->save();

            $company = Company::where('ceo_id', $employee['id'])->first();
            if(empty($company)) {
                $company = new Company();
                $company['ceo_id'] = $employee['id'];
            }
            $company['company_name'] = $company_name;
            $company->save();

            return redirect('apply')->with('success', '保存成功');
        } else {
            $user = User::find($user['id']);
            Session::put('user', $user);

            if($user['apply_status'] == 'approve') {
                return redirect()->route('people');
            }

            $name = null;
            $company = null;
            $position = null;
            $rank = null;
            $question1 = null;
            $question2 = null;
            $question3 = null;

            $employee = Employee::where('user_id', $user['id'])->first();
            if(!empty($employee)) {
                $name = $employee['real_name'];
                $position = $employee['position'];

                $company = Company::where('ceo_id', $employee['id'])->first();
                if(!empty($company)) {
                    $company = $company['company_name'];
                }
            }

            $rank = User::where('created_at', '<=', $user['created_at'])
                ->where('apply_status', 'applying')
                ->count();

            $share_link = url('invite/' . $user['id']);

            return view('apply', [
                'name' => $name,
                'company' => $company,
                'position' => $position,
                'rank' => $rank,
                'question1' => $question1,
                'question2' => $question2,
                'question3' => $question3,
                'share_link' => $share_link,
            ]);
        }
    }
}
