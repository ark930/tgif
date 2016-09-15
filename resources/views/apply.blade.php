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
            <label for="inputName">姓名</label>
            <span class="text-danger">{{ Session::get('error1') }}</span>
            <input type="text" class="form-control" id="inputName" name="name" value="{{ $name }}">
        </div>
        <div class="form-group">
            <label for="inputCompany">公司</label>
            <span class="text-danger">{{ Session::get('error2') }}</span>
            <input type="text" class="form-control" id="inputCompany" name="company" value="{{ $company }}">
        </div>
        <div class="form-group">
            <label for="inputPosition">职位</label>
            <span class="text-danger">{{ Session::get('error3') }}</span>
            <input type="text" class="form-control" id="inputPosition" name="position" value="{{ $position }}">
        </div>

        <label>问题</label>
        <div class="list-group">
            <a class="list-group-item">1. 你是谁?</a>
            <a class="list-group-item">2. 你从哪里来?</a>
            <a class="list-group-item">3. 你要到哪里去?</a>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-default">保存</button>
            <span class="text-primary">{{ Session::get('success') }}</span>
        </div>
    </form>

    <hr>





@endsection