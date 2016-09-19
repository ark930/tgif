@extends('layout')

@section('left_top_button')
    @if(Session::has('user'))
        <a href="{{ url()->route('people') }}" class="right btn_login btn_cta">{{ Session::get('user')['tel'] }}</a>
    @else
        <a href="{{ url('/login') }}" class="right btn_login btn_cta">登入</a>
    @endif
@endsection

@section('content')
    <div class="hero">
        <div class="raw_lineline">
            <div class="grid g1">
                <h1 class="title">CEO 了解自己团队多一点，<br class="mob-none">每周只需 30 秒。</h1>
                <p>封测中。</p>
            </div>
            <div class="grid g2">
            </div>
        </div>
    </div>
    <div class="phone">
        <form class="g1" method="post">
            {{ csrf_field() }}
            <input type="phone" placeholder="输入你的手机" name="username">
            <button type="submit" class="btn_success">免费试用</button>
        </form>
        <div class="g2">
            <p>一分钟设置完毕 · 无订阅费</p>
        </div>
    </div>

@endsection