@extends('layout')

@section('left_top_button')
    <a href="{{ url('/logout') }}" class="right btn_login btn_cta">{{ Session::get('user')['tel'] }}</a>
@endsection

@section('content')
    <ul class="nav nav-pills" role="tablist">
        <li role="presentation"><a href="{{ url()->route('timeline') }}">时间线</a></li>
        <li role="presentation" class="active"><a href="{{ url()->route('people') }}">个人</a></li>
        <li role="presentation"><a href="{{ url()->route('surveys') }}">问题</a></li>
    </ul>

    <hr>


@endsection