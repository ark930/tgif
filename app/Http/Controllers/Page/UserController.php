<?php

namespace App\Http\Controllers\Page;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Form;
use App\Models\Question;
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

    public function applyGet(Request $request)
    {
        $user = Session::get('user');
        $user = User::find($user['id']);
        Session::put('user', $user);

        if($user['apply_status'] == 'approve') {
            return redirect()->route('people');
        }

        return view('apply', $user->getApplyInfo());
    }

    public function applyPost(Request $request)
    {
        $user = Session::get('user');

        $name = $request->input('name');
        $company_name = $request->input('company_name');
        $position = $request->input('position');
        $question1 = $request->input('question1');

        if(empty($name)) {
            return redirect()->back()->with('error1', '(请填写名称)');
        }

        if(empty($company_name)) {
            return redirect()->back()->with('error2', '(请填写公司名)');
        }

        if(empty($position)) {
            return redirect()->back()->with('error3', '(请填写职位)');
        }

        if(empty($question1)) {
            return redirect()->back()->with('error4', '(请填写问题)');
        }

        $user['real_name'] = $name;

        $employee = $user->nowEmployee;
        if(empty($employee)) {
            $employee = new Employee();
            $employee['user_id'] = $user['id'];
            $employee['role'] = 'owner';
        }
        $employee['real_name'] = $name;
        $employee['position'] = $position;

        $company = $employee->company;
        if(empty($company)) {
            $company = new Company();
            $company['ceo_id'] = $employee['id'];
            $company['company_name'] = $company_name;
            $company->save();

            $company->employees()->save($employee);
            $company->ceo()->associate($employee);
//            $employee->company()->associate($company);
            $employee['company_id'] = $company['id'];
        } else {
            $company['company_name'] = $company_name;
        }
        $company->save();
        $employee->save();
//        $user->employees()->save($employee);
        $user->nowEmployee()->associate($employee);
        $user->save();

        $question = new Question();
        $question['question'] = $question1;
        $question->save();

        $form = new Form();
        $form['editor_id'] = $employee['id'];
        $form->save();
        $form->questions()->attach($question['id']);

        $company['form_id'] = $form['id'];
        $company->save();

        return redirect('apply')->with('success', '保存成功');
    }

    public function invite($user_id)
    {
        $user = User::find($user_id);

        if(!empty($user)) {
            $user['invite_count'] += 1;
            $user->save();
        }

        return redirect('/');
    }

}
