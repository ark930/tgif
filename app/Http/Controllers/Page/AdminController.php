<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private $per_page = 10;

    public function index(Request $request)
    {
        return redirect()->route('admin_apply');
    }

    public function apply(Request $request)
    {
        $users = User::where('apply_status', 'applying')
            ->orderBy('invite_count', 'DESC')
            ->paginate($this->per_page);

        return view('admin.apply', ['users' => $users]);
    }

    public function approve(Request $request)
    {
        $users = User::where('apply_status', 'approve')
            ->orderBy('invite_count', 'DESC')
            ->paginate($this->per_page);

        return view('admin.apply', ['users' => $users]);
    }

    public function reject(Request $request)
    {
        $users = User::where('apply_status', 'reject')
            ->orderBy('invite_count', 'DESC')
            ->paginate($this->per_page);

        return view('admin.apply', ['users' => $users]);
    }

    public function data(Request $request)
    {
        return view('admin.data');
    }

    public function doApply(Request $request, $action, $user_id)
    {
        $user = User::find($user_id);
        if(!empty($user)) {
            $user['apply_status'] = $action;
            $user->save();
        }

        return redirect()->back()->with('success', '操作成功');
    }
}