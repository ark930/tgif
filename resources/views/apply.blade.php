@extends('layout')

@section('content')

    <h2>审核</h2>

    <hr>

    <p class="text-info">目前处于内侧阶段。</p>
    <p class="text-info">注册账户需要审核。您正处于审核队列中, 目前排在第 {{ $rank }} 位。</p>

    <hr>

    <form role="form" method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="inputName">你的姓名</label>
            <span class="text-danger">{{ Session::get('error1') }}</span>
            <input type="text" class="form-control" id="inputName" name="name" value="{{ $name }}">
        </div>
        <div class="form-group">
            <label for="inputCompany">你的公司</label>
            <span class="text-danger">{{ Session::get('error2') }}</span>
            <input type="text" class="form-control" id="inputCompany" name="company" value="{{ $company }}">
        </div>
        <div class="form-group">
            <label for="inputPosition">你的职位</label>
            <span class="text-danger">{{ Session::get('error3') }}</span>
            <input type="text" class="form-control" id="inputPosition" name="position" value="{{ $position }}">
        </div>

        <div class="form-group">
            <label for="inputQuestion1">问题</label>
            <input type="text" class="form-control" id="inputQuestion1" name="question1" value="{{ $question1 }}">
        </div>

        {{--<div class="form-group">--}}
            {{--<label for="inputQuestion1">问题2</label>--}}
            {{--<input type="text" class="form-control" id="inputQuestion2" name="question2" value="{{ $question2 }}">--}}
        {{--</div>--}}

        {{--<div class="form-group">--}}
            {{--<label for="inputQuestion1">问题3</label>--}}
            {{--<input type="text" class="form-control" id="inputQuestion3" name="question3" value="{{ $question3 }}">--}}
        {{--</div>--}}

        <div class="form-group">
            <button type="submit" class="btn btn-default">保存</button>
            <span class="text-primary">{{ Session::get('success') }}</span>
        </div>
    </form>

    <hr>

    <p>邀请好友链接: {{ $share_link }}</p>



@endsection