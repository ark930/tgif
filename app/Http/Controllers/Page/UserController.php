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
    public function home(Request $request)
    {
        $inviter_id = $request->input('from');

        if(isset($inviter_id)) {
            $inviter = User::find($inviter_id);

            if(!empty($inviter)) {
                Session::put('inviter_id', $inviter_id);
            }
        }

        return view('home');
    }

    public function freeTrial(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|regex:/^1\d{10}$/',
        ], [
            'username.required' => '请填写手机号',
            'username.regex' => '请填写正确的手机号',
        ]);

        $username = $request->input('username');

        return redirect('/login')->with([
            'username' => $username,
            'get_verify_code' => true,
        ]);
    }

    public function login(Request $request)
    {
        if($request->isMethod('post')) {

            $this->validate($request, [
                'username' => 'required|regex:/^1\d{10}$/',
                'password' => 'required|regex:/^\d{6}$/',
            ], [
                'username.required' => '请填写手机号',
                'username.regex' => '请填写正确的手机号',
                'password.required' => '请填写验证码',
                'password.regex' => '验证码格式不正确',
            ]);

            $username = $request->input('username');
            $password = $request->input('password');

            $user = User::where('tel', $username)->first();

            if(empty($user)) {
                return redirect()->back()->withErrors('登录失败')->withInput();
            }

            if($user->ifVerifyCodeExpired()) {
                return redirect()->back()->withErrors('验证码过期, 请重新获取')->withInput();
            }

            if($user->ifVerifyCodeRetryTimesExceed()) {
                return redirect()->back()->withErrors('验证码输入错误次数过多, 已失效, 请重新获取')->withInput();
            }

            if($user->ifVerifyCodeWrong($password)) {
                return redirect()->back()->withErrors('验证码错误')->withInput();
            }

            // 保存邀请人信息
            if(Session::has('inviter_id')) {
                $inviter_id = Session::pull('inviter_id');
                $inviter = User::find($inviter_id);

                if(!empty($inviter)) {
                    $user->inviter()->associate($inviter);
                    $user->save();
                    $inviter['invite_count'] += 1;
                    $inviter->save();
                }
            }

            $user->disableVerifyCode();
            $user->ifFirstLogin();
            $user->updateLastLogin();

            Session::put('user', $user);

            if($user['apply_status'] == 'approve') {
                return redirect()->route('people');
            } else {
                return redirect('apply');
            }
        } else {
//            if(Session::has('user')) {
//                return redirect()->route('people');
//            }
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

        $save_question = $request->input('save_question');
        $save_basic_info = $request->input('save_basic_info');

        if(isset($save_question)) {
            // 保存问题
//            $this->validate($request, [
//                'question1' => 'required',
//            ], [
//                'question1.required' => '请填写问题',
//            ]);

            $question1 = $request->input('question1');
            $this->save_apply_info($user, $question1);
        } else if(isset($save_basic_info)) {
            // 保存基本信息
//            $this->validate($request, [
//                'name' => 'required',
//                'company_name' => 'required',
//                'position' => 'required',
//                'company_count' => 'required|integer|min:1',
//            ], [
//                'name.required' => '请填写你的姓名',
//                'company_name.required' => '请填写你的公司',
//                'position.required' => '请填写你的职位',
//                'company_count.required' => '请填写公司人数',
//                'company_count.integer' => '公司人数必须是个数字',
//                'company_count.min' => '公司人数至少是1',
//            ]);

            $name = $request->input('name');
            $company_name = $request->input('company_name');
            $position = $request->input('position');
            $company_count = $request->input('company_count');

            $this->save_apply_info($user, null, $name, $position, $company_name, $company_count);
        } else {
            // 异常请求
            return redirect('apply');
        }

        return redirect('apply')->with('success', '保存成功');
    }

    private function save_apply_info($user, $question1, $name = null, $position = null, $company_name = null, $company_count = null)
    {
        $user['real_name'] = $name;

        $employee = $user->nowEmployee;
        if(empty($employee)) {
            $employee = new Employee();
            $employee['user_id'] = $user['id'];
            $employee['role'] = 'owner';
        }
        $employee['real_name'] = empty($name) ? $employee['real_name'] : $name;
        $employee['position'] = empty($position) ? $employee['position'] : $position;

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
        }
        $company['company_name'] = empty($company_name) ? $company['company_name'] : $company_name;
        $company['company_count'] = empty($company_count) ? $company['company_count'] : $company_count;
        $company->save();
        $employee->save();
//        $user->employees()->save($employee);
        $user->nowEmployee()->associate($employee);
        $user['apply_status'] = 'applying';
        $user->save();

        if(!empty($question1)) {
            $question = new Question();
            $question['question'] = $question1;
            $question->save();

            $form = new Form();
            $form['editor_id'] = $employee['id'];
            $form->save();
            $form->questions()->attach($question['id']);

            $company['form_id'] = $form['id'];
            $company->save();
        }
    }

}
