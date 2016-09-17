@extends('layout')

@section('css')
    <link href="{{ asset('style/queue.css') }}" rel="stylesheet" media="screen">
@show

@section('content')
    <div class="header raw_lr">
        <a href="{{ url('/') }}" class="home left">
            <img src="{{ asset('img/logo.svg') }}" alt="TGIF" class="logo">
        </a>
    </div>
    <div class="hero"><span id="queue">{{ $rank }}</span><br>
        <p>个用户正排在你前面</p>
        <p>要更早开始使用，你可以通过一下几种方式</p>
    </div>
    <div class="step step1">
        <div class="number">1</div>
        <form method="post">
            {{ csrf_field() }}
            <input type="hidden" name="save_question" value="true">
            <h6 class="title">说说你最想问你的员工哪些问题？</h6>
            <input id="fill_questions" type="text" name="question1" value="{{ null !== old('question1') ? old('question1') : $question1 }}">
            <span class="text-danger">{{ Session::get('error_question1') }}</span>
            <button id="submit_question" class="submit">保存</button>
        </form>
    </div>
    <div class="step step2">
        <div class="number">2</div>
        <p>通过你的分享链接，邀请你的 CEO 朋友一起来使用</p><span>{{ $invite_link }}</span>
    </div>
    <div class="step step3">
        <div class="number">3</div>
        <h6>完善你的基本信息</h6>
        <form method="post">
            {{ csrf_field() }}
            <input type="hidden" name="save_basic_info" value="true">
            <div class="formitem">
                <h6 class="title">你的姓名</h6>
                <input type="text" name="name" value="{{ null !== old('name') ? old('name') : $name }}">
                <span class="text-danger">{{ Session::get('error_name') }}</span>
            </div>
            <div class="formitem">
                <h6 class="title">你的公司</h6>
                <input type="text" name="company_name" value="{{ null !== old('company_name') ? old('company_name') : $company_name }}">
                <span class="text-danger">{{ Session::get('error_company_name') }}</span>
            </div>
            <div class="formitem">
                <h6 class="title">你的职位</h6>
                <input type="text" name="position" value="{{ null !== old('position') ? old('position') : $position }}">
                <span class="text-danger">{{ Session::get('error_position') }}</span>
            </div>
            <div class="formitem">
                <h6 class="title">公司人数</h6>
                <input type="text" name="company_count" value="{{ null !== old('company_count') ? old('company_count') : $company_count }}">
                <span class="text-danger">{{ Session::get('error_company_count') }}</span>
            </div>
            <button id="submit_personinfo" class="submit">保存</button>
        </form>
    </div>

@endsection