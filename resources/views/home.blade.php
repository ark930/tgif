@extends('layout')

@section('content')
    <div class="header raw_lr">
        <a href="{{ url('/') }}" class="home left">
            <img src="{{ asset('img/logo.svg') }}" alt="TGIF" class="logo">
        </a>
        <a href="{{ url('/login') }}" class="right btn_login">登入</a>
    </div>
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
        <div class="g1">
            <input type="phone" placeholder="输入你的手机">
            <button>免费试用</button>
        </div>
        <div class="g2">
            <p>一分钟设置完毕 · 无订阅费</p>
        </div>
    </div>
@endsection